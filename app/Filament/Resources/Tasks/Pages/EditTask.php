<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('complete')
                ->label('Tamamlandı')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => ! $this->record->isCompleted())
                ->action(function () {
                    $next = $this->record->complete();

                    Notification::make()
                        ->title('İş tamamlandı')
                        ->body($next ? 'Bir sonraki tekrar oluşturuldu: '.$next->dueLabel() : null)
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Action::make('reopen')
                ->label('Geri Aç')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->visible(fn (): bool => $this->record->isCompleted())
                ->action(function () {
                    $this->record->reopen();
                    $this->refreshFormData(['status']);
                }),
            DeleteAction::make(),
        ];
    }

    /** Durum formdan "Tamamlandı" yapılırsa da tekrar eden işin sonrakisi oluşsun. */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $completing = ($data['status'] ?? null) === 'tamamlandi' && ! $record->isCompleted();

        if ($completing) {
            $data['status'] = $record->status;
        }

        $record->update($data);

        if ($completing) {
            $record->complete();
        }

        return $record;
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
