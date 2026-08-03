<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    public function getPollingInterval(): ?string
    {
        return '15s';
    }

    public function getTitle(): string
    {
        return Str::limit($this->record->subject, 55);
    }

    public function getBreadcrumb(): string
    {
        return 'Düzenle';
    }

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

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();
        
        if (!empty($data['resolution_note']) && !in_array($data['status'], ['işlem yapıldı', 'çözüldü', 'iptal'])) {
            $this->dispatch('open-resolve-confirmation-modal');
            return;
        }

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    #[\Livewire\Attributes\On('confirm-resolve-save')]
    public function forceSaveAsResolved(): void
    {
        $state = $this->form->getState();
        $state['status'] = 'çözüldü';
        
        // Update form state
        $this->form->fill($state);
        
        parent::save(true, true);
    }
}
