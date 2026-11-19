<?php

namespace App\Services;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * İş listesi hatırlatmalarını panelin bildirim zilinine yazar.
 *
 * Sunucuda zamanlayıcı ya da kuyruk işçisi olmadığı için panel açıkken yapılan
 * 30 saniyelik kontrolle çalışır (TicketWatcher). Her bildirim veritabanında
 * işaretlendiği için birden fazla sekme açık olsa da yalnızca bir kez gider.
 */
class TaskReminders
{
    /** Bir seferde bundan fazla bildirim çıkacaksa tek bir özet gönderilir. */
    private const MAX_SEPARATE = 3;

    /** Panel kontrolü her 30 saniyede bir gelir; kullanıcı başına dakikada bir çalışmak yeterli. */
    public static function sendForThrottled(?User $user): void
    {
        if ($user && Cache::add("task-reminders:{$user->id}", true, 55)) {
            static::sendFor($user);
        }
    }

    /** @return int gönderilen bildirim sayısı */
    public static function sendFor(User $user): int
    {
        $sent = static::sendDigest($user);

        $dueSoon = Task::query()->ownedBy($user)->open()
            ->whereNull('reminded_at')
            ->whereNotNull('remind_at')
            ->where('remind_at', '<=', now())
            ->where('due_at', '>', now())
            ->get()
            ->filter(fn (Task $task) => static::claim($task, 'reminded_at'));

        $overdue = Task::query()->ownedBy($user)->open()
            ->whereNull('overdue_notified_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now())
            ->get()
            ->filter(fn (Task $task) => static::claim($task, 'overdue_notified_at'));

        if ($dueSoon->count() + $overdue->count() > self::MAX_SEPARATE) {
            static::send($user, static::bulk($dueSoon, $overdue));

            return $sent + 1;
        }

        $dueSoon->each(fn (Task $task) => static::send($user, static::dueSoon($task)));
        $overdue->each(fn (Task $task) => static::send($user, static::overdue($task)));

        return $sent + $dueSoon->count() + $overdue->count();
    }

    /** Zamanlayıcı kurulursa tüm kullanıcılar için çalıştırılır (tasks:remind). */
    public static function sendAll(): int
    {
        return User::query()
            ->whereIn('id', Task::query()->open()->select('user_id'))
            ->get()
            ->sum(fn (User $user) => static::sendFor($user));
    }

    /** Aynı bildirimin iki sekmeden iki kez gitmemesi için kaydı koşullu işaretler. */
    private static function claim(Task $task, string $column): bool
    {
        $claimed = Task::query()->whereKey($task->id)->whereNull($column)->update([
            $column => now(),
            // Süresi geçen işe ayrıca "yaklaşıyor" hatırlatması gitmesin
            ...($column === 'overdue_notified_at' ? ['reminded_at' => now()] : []),
        ]) === 1;

        return $claimed;
    }

    /** Günün ilk kontrolünde bugün biten ve geciken işlerin özeti. */
    private static function sendDigest(User $user): int
    {
        $today = Task::query()->ownedBy($user)->dueToday()->count();
        $late = Task::query()->ownedBy($user)->overdue()->count();

        if ($today + $late === 0 || ! Cache::add("task-digest:{$user->id}:".today()->toDateString(), true, now()->endOfDay())) {
            return 0;
        }

        $parts = array_filter([
            $today ? "bugün bitmesi gereken {$today} işiniz" : null,
            $late ? "süresi geçmiş {$late} işiniz" : null,
        ]);

        static::send($user, Notification::make()
            ->title('Günün işleri')
            ->body('İş listenizde '.implode(' ve ', $parts).' var.')
            ->icon('heroicon-o-clipboard-document-list')
            ->iconColor($late ? 'danger' : 'info')
            ->actions([static::listAction()]));

        return 1;
    }

    private static function dueSoon(Task $task): Notification
    {
        return Notification::make()
            ->title('Son tarihi yaklaşan işiniz var')
            ->body("{$task->title} · Bitiş: {$task->due_at->translatedFormat('j F H:i')} ({$task->due_at->diffForHumans()})")
            ->icon('heroicon-o-clock')
            ->iconColor('warning')
            ->actions([static::openAction($task)]);
    }

    private static function overdue(Task $task): Notification
    {
        return Notification::make()
            ->title('Süresi geçen işiniz var')
            ->body("{$task->title} · {$task->due_at->translatedFormat('j F H:i')} tarihinde bitmesi gerekiyordu, henüz tamamlanmadı.")
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('danger')
            ->actions([static::openAction($task)]);
    }

    private static function bulk(Collection $dueSoon, Collection $overdue): Notification
    {
        $parts = array_filter([
            $overdue->count() ? "süresi geçen {$overdue->count()}" : null,
            $dueSoon->count() ? "son tarihi yaklaşan {$dueSoon->count()}" : null,
        ]);

        return Notification::make()
            ->title('Bekleyen işleriniz var')
            ->body('İş listenizde '.implode(' ve ', $parts).' iş var.')
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor($overdue->count() ? 'danger' : 'warning')
            ->actions([static::listAction()]);
    }

    private static function openAction(Task $task): Action
    {
        return Action::make('open')
            ->label('İşi Aç')
            ->button()
            ->markAsRead()
            ->url(TaskResource::getUrl('edit', ['record' => $task], panel: 'admin'));
    }

    private static function listAction(): Action
    {
        return Action::make('list')
            ->label('İş Listesini Aç')
            ->button()
            ->markAsRead()
            ->url(TaskResource::getUrl('index', panel: 'admin'));
    }

    /** Kuyruk işçisi olmadığı için bildirim kuyruğa değil doğrudan yazılır. */
    public static function send(User $user, Notification $notification): void
    {
        $user->notifyNow($notification->toDatabase());
    }
}
