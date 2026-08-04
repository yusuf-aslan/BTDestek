<?php
namespace App\Filament\Resources\TicketStatuses\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class TicketStatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('sort_order')->reorderable('sort_order')->columns([
            TextColumn::make('name')->label('Durum')->badge()->color(fn($record)=>$record->color)->searchable(),
            TextColumn::make('key')->label('Sistem Anahtarı')->searchable(),
            TextColumn::make('destination')->label('Gösterileceği Sekme')->formatStateUsing(fn($state)=>match($state){'pending'=>'Bekleyenler','solved'=>'Çözülenler','cancelled'=>'İptal Edilenler',default=>$state})->badge(),
            IconColumn::make('closes_ticket')->label('Talebi Kapatır')->boolean(),
            IconColumn::make('is_active')->label('Aktif')->boolean(),
            TextColumn::make('sort_order')->label('Sıra')->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make()
                ->requiresConfirmation()
                ->visible(fn ($record): bool => !\App\Models\Ticket::where('status', $record->key)->exists()),
        ]);
    }
}
