<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save_top')
                ->label('Değişiklikleri Kaydet')
                ->color('primary')
                ->action('save'),
            Action::make('cancel_top')
                ->label('İptal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
            Action::make('print')
                ->label('Yazdır')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn ($record) => route('admin.tickets.print', $record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
