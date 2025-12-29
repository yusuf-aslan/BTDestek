<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if (isset($data['save_as_template']) && $data['save_as_template']) {
            \App\Models\AnnouncementTemplate::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
            ]);
            
            \Filament\Notifications\Notification::make()
                ->title('Şablon Kaydedildi')
                ->success()
                ->send();
        }
    }
}
