<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Ticket;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    /** Talep ekranındaki "İş Listeme Ekle" bağlantısı talebi seçili getirir. */
    public function mount(): void
    {
        parent::mount();

        $ticket = request()->integer('ticket')
            ? Ticket::query()->visibleTo(auth()->user())->find(request()->integer('ticket'))
            : null;

        if ($ticket) {
            $this->form->fill([
                ...$this->form->getRawState(),
                'ticket_id' => $ticket->id,
                'title' => str("{$ticket->tracking_number} · {$ticket->subject}")->limit(200)->toString(),
                'priority' => array_key_exists($ticket->priority, \App\Models\Task::PRIORITIES) ? $ticket->priority : 'orta',
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
