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
        
        $resolutionNoteChanged = $this->normalizeRichText($data['resolution_note'] ?? null)
            !== $this->normalizeRichText($this->record->resolution_note);

        if ($resolutionNoteChanged && !empty($data['resolution_note']) && !TicketStatus::isClosed($data['status'])) {
            $this->dispatch('open-resolve-confirmation-modal');
            return;
        }

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    #[\Livewire\Attributes\On('confirm-resolve-save')]
    public function forceSaveAsResolved(): void
    {
        $state = $this->form->getState();
        $state['status'] = TicketStatus::resolutionKey();
        
        // Update form state
        $this->form->fill($state);
        
        parent::save(true, true);
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
