<?php

namespace App\Filament\Resources\TicketAttachments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Önizleme')
                    ->square()
                    ->disk('public')
                    ->visibility('public')
                    ->defaultImageUrl(fn ($record) => match(true) {
                        str_contains($record->file_type, 'pdf') => url('https://cdn-icons-png.flaticon.com/512/337/337946.png'),
                        str_contains($record->file_type, 'text') => url('https://cdn-icons-png.flaticon.com/512/337/337956.png'),
                        default => url('https://cdn-icons-png.flaticon.com/512/2906/2906140.png'),
                    }),
                TextColumn::make('file_name')
                    ->label('Dosya Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ticket.tracking_number')
                    ->label('İlgili Talep')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->ticket_id ? "/admin/tickets/{$record->ticket_id}/edit" : null),
                TextColumn::make('file_type')
                    ->label('Tür')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('file_size')
                    ->label('Boyut')
                    ->formatStateUsing(fn ($state) => round($state / 1024, 1) . ' KB')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('download')
                    ->label('İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('tickets.attachments.download', $record))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
