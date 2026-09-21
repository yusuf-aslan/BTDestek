<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use App\Models\TicketStatus;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected string $view = 'filament.pages.edit-ticket-locked';

    #[\Livewire\Attributes\Locked]
    public string $editToken = '';

    #[\Livewire\Attributes\Locked]
    public bool $hasEditLock = false;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->authorizeAccess();
        $this->editToken = (string) Str::uuid();
        try {
            app(\App\Services\TicketEditLock::class)->acquire((int) $this->record->id, $this->editToken);
            $this->hasEditLock = true;
            parent::mount($record);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->lockWarning($exception);
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    private function lockWarning(\Illuminate\Validation\ValidationException $exception): void
    {
        \Filament\Notifications\Notification::make()->warning()->title('Talep kullanımda')
            ->body(collect($exception->errors())->flatten()->first())->persistent()->send();
    }

    public function heartbeatEditLock(): void
    {
        if (! $this->hasEditLock) return;
        try {
            $service = app(\App\Services\TicketEditLock::class);
            $service->run((int) $this->record->id, $this->editToken,
                fn () => $service->acquire((int) $this->record->id, $this->editToken));
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->hasEditLock = false;
            $this->lockWarning($exception);
        }
    }

    public function releaseTicket(): void
    {
        app(\App\Services\TicketEditLock::class)->release((int) $this->record->id, $this->editToken);
        $this->hasEditLock = false;
        $this->redirect($this->getRedirectUrl());
    }

    public function releaseIdleTicket(): void
    {
        if (! $this->hasEditLock) return;

        app(\App\Services\TicketEditLock::class)->release((int) $this->record->id, $this->editToken);
        $this->hasEditLock = false;

        \Filament\Notifications\Notification::make()
            ->warning()
            ->title('Talep kilidi bırakıldı')
            ->body('2 dakika işlem yapılmadığı için talep diğer kullanıcıların düzenlemesine açıldı.')
            ->send();

        $this->redirect($this->getRedirectUrl());
    }

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
        if (! $this->hasEditLock) return [];
        return [
            Action::make('save_top')
                ->label('Değişiklikleri Kaydet')
                ->color('primary')
                ->action('save'),
            Action::make('cancel_top')
                ->label('Talebi Bırak')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Talep bırakılsın mı?')
                ->modalDescription('Kaydedilmemiş değişiklikler silinecek ve diğer kullanıcılar talebi açabilecek.')
                ->action('releaseTicket'),
            Action::make('print')
                ->label('Yazdır')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn ($record) => route('admin.tickets.print', $record))
                ->openUrlInNewTab(),
            DeleteAction::make()->using(fn ($record) => app(\App\Services\TicketEditLock::class)
                ->run((int) $record->id, $this->editToken, fn () => $record->delete())),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            app(\App\Services\TicketEditLock::class)->run((int) $this->record->id, $this->editToken,
                fn () => $this->saveWithLock($shouldRedirect, $shouldSendSavedNotification));
        } catch (\Illuminate\Validation\ValidationException $exception) {
            if (! isset($exception->errors()['edit_lock'])) throw $exception;
            $this->hasEditLock = false;
            $this->lockWarning($exception);
        }
    }

    private function saveWithLock(bool $shouldRedirect, bool $shouldSendSavedNotification): void
    {
        $data = $this->form->getState();
        
        $resolutionNoteChanged = $this->normalizeRichText($data['resolution_note'] ?? null)
            !== $this->normalizeRichText($this->record->resolution_note);

        if ($resolutionNoteChanged && !empty($data['resolution_note']) && !TicketStatus::isClosed($data['status'])) {
            $this->dispatch('open-resolve-confirmation-modal');
            return;
        }

        parent::save($shouldRedirect, $shouldSendSavedNotification);
        if ($shouldRedirect) {
            app(\App\Services\TicketEditLock::class)->release((int) $this->record->id, $this->editToken);
        }
    }

    #[\Livewire\Attributes\On('confirm-resolve-save')]
    public function forceSaveAsResolved(): void
    {
        $state = $this->data;
        $state['status'] = TicketStatus::resolutionKey();
        
        // Update form state
        $this->form->fill($state);
        
        $this->save(true, true);
    }

    public function saveFormComponentOnly(\Filament\Schemas\Components\Component $component): void
    {
        app(\App\Services\TicketEditLock::class)->run((int) $this->record->id, $this->editToken,
            fn () => parent::saveFormComponentOnly($component));
    }

    private function normalizeRichText(?string $content): string
    {
        if (blank($content)) {
            return '';
        }

        $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        preg_match_all('/<img\b[^>]*\bsrc=["\']([^"\']+)["\']/i', $decoded, $images);

        $text = preg_replace('/<br\s*\/?>/i', "\n", $decoded);
        $text = preg_replace('/<\/(p|div|li|h[1-6]|blockquote)>/i', "\n", $text);
        $text = strip_tags($text);
        $text = str_replace("\u{00A0}", ' ', $text);
        $text = preg_replace('/[\h\v]+/u', ' ', $text);
        $text = trim($text);
        $imageSources = $images[1] ?? [];

        if ($text === '' && $imageSources === []) {
            return '';
        }

        return $text . '|' . implode('|', $imageSources);
    }
}
