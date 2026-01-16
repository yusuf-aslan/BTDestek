<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Geçmiş Destek Talepleri';

    protected static ?string $modelLabel = 'Talep';
    protected static ?string $pluralModelLabel = 'Talepler';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(50),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'yeni' => 'gray',
                        'işlemde' => 'warning',
                        'beklemede' => 'info',
                        'çözüldü' => 'success',
                        'iptal' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label('Görüntüle')
                    ->url(fn ($record): string => TicketResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                //
            ]);
    }
}
