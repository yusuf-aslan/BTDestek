<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected string $view = 'filament.pages.view-ticket';

    #[\Livewire\Attributes\Locked]
    public bool $isEditLocked = false;

    #[\Livewire\Attributes\Locked]
    public ?string $lockOwnerName = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->refreshLockStatus();

        if (! $this->isEditLocked) {
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
        }
    }

    public function refreshLockStatus(): void
    {
        $this->record->refresh();

        $this->isEditLocked = filled($this->record->edit_lock_token)
            && filled($this->record->edit_lock_expires_at)
            && $this->record->edit_lock_expires_at > now()->toDateTimeString();

        $this->lockOwnerName = $this->isEditLocked
            ? User::find($this->record->edit_lock_user_id)?->name
            : null;
    }

    public function getTitle(): string
    {
        return Str::limit($this->record->subject, 55);
    }

    public function getBreadcrumb(): string
    {
        return 'Görüntüle';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label(fn (): string => $this->isEditLocked ? 'Düzenlemeyi Tekrar Dene' : 'Düzenlemeye Geç')
                ->icon('heroicon-o-pencil-square')
                ->color(fn (): string => $this->isEditLocked ? 'gray' : 'primary')
                ->url(fn (): string => $this->getResource()::getUrl('edit', ['record' => $this->record])),
            Action::make('print')
                ->label('Yazdır')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('admin.tickets.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}
