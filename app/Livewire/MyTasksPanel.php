<?php

namespace App\Livewire;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Anasayfadaki "İş Listem" kutusu: geciken ve yaklaşan işler, tek tıkla tamamlama ve hızlı ekleme.
 */
class MyTasksPanel extends Component
{
    private const LIMIT = 6;

    public string $title = '';

    public string $due = 'today';

    public function complete(int $id): void
    {
        $task = Task::query()->ownedBy(Auth::user())->open()->find($id);

        if (! $task) {
            return;
        }

        $next = $task->complete();

        Notification::make()
            ->title('İş tamamlandı')
            ->body($next ? 'Bir sonraki tekrar oluşturuldu: '.$next->dueLabel() : $task->title)
            ->success()
            ->send();
    }

    public function add(): void
    {
        $this->validate(
            ['title' => 'required|string|max:200', 'due' => 'required|in:today,tomorrow,week,none'],
            [],
            ['title' => 'İş']
        );

        Task::create([
            'user_id' => Auth::id(),
            'title' => trim($this->title),
            'due_at' => match ($this->due) {
                'today' => now()->setTime(17, 0)->max(now()->addHour()->startOfHour()),
                'tomorrow' => now()->addDay()->setTime(17, 0),
                'week' => tap(now()->endOfWeek(\Carbon\CarbonInterface::FRIDAY)->setTime(17, 0), fn ($friday) => $friday->isPast() ? $friday->addWeek() : $friday),
                default => null,
            },
            'remind_before_minutes' => $this->due === 'none' ? null : 60,
            'priority' => 'orta',
            'status' => 'bekliyor',
        ]);

        $this->reset('title');
    }

    public function render()
    {
        $base = fn () => Task::query()->ownedBy(Auth::user())->open();

        return view('livewire.my-tasks-panel', [
            'tasks' => $base()->with('ticket')->byUrgency()->limit(self::LIMIT)->get(),
            'openCount' => $base()->count(),
            'overdueCount' => $base()->overdue()->count(),
            'todayCount' => $base()->whereDate('due_at', today())->where('due_at', '>=', now())->count(),
            'listUrl' => TaskResource::getUrl('index'),
            'createUrl' => TaskResource::getUrl('create'),
        ]);
    }
}
