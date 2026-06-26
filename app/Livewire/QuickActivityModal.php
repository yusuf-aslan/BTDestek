<?php

namespace App\Livewire;

use App\Models\Activity;
use Livewire\Component;
use Filament\Notifications\Notification;

class QuickActivityModal extends Component
{
    public bool $isOpen = false;

    public string $activity_type = 'Telefon Desteği';
    public ?int $duration = 15;
    public string $department = '';
    public ?string $activity_date = null;
    public string $description = '';

    protected array $rules = [
        'activity_type' => 'required|string',
        'duration' => 'required|integer|min:1',
        'department' => 'required|string|max:255',
        'activity_date' => 'required|date',
        'description' => 'required|string',
    ];

    protected array $validationAttributes = [
        'activity_type' => 'Faaliyet Türü',
        'duration' => 'Harcanan Süre',
        'department' => 'Bölüm/Oda',
        'activity_date' => 'Faaliyet Tarihi',
        'description' => 'Açıklama',
    ];

    public function mount()
    {
        $this->activity_date = now()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        $this->validate();

        Activity::create([
            'user_id' => auth()->id(),
            'activity_type' => $this->activity_type,
            'duration' => $this->duration,
            'department' => $this->department,
            'activity_date' => $this->activity_date,
            'description' => $this->description,
        ]);

        $this->isOpen = false;

        // Reset fields but keep date and type as defaults
        $this->reset(['duration', 'department', 'description']);
        $this->duration = 15;

        // Dispatch browser notification or refresh event if needed
        $this->dispatch('activity-saved');

        Notification::make()
            ->title('Faaliyet Başarıyla Eklendi')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.quick-activity-modal');
    }
}
