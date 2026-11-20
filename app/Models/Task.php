<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Kişiye özel iş listesi kaydı. Her kullanıcı yalnızca kendi işlerini görür.
 */
class Task extends Model
{
    public const STATUSES = [
        'bekliyor' => 'Bekliyor',
        'devam' => 'Devam Ediyor',
        'tamamlandi' => 'Tamamlandı',
    ];

    public const PRIORITIES = [
        'düşük' => 'Düşük',
        'orta' => 'Orta',
        'yüksek' => 'Yüksek',
        'acil' => 'Acil',
    ];

    public const PRIORITY_COLORS = [
        'düşük' => 'gray',
        'orta' => 'info',
        'yüksek' => 'warning',
        'acil' => 'danger',
    ];

    /** Bitişten kaç dakika önce hatırlatılacağı; 0 bitiş anında demektir. */
    public const REMINDERS = [
        0 => 'Bitiş anında',
        15 => '15 dakika önce',
        60 => '1 saat önce',
        180 => '3 saat önce',
        1440 => '1 gün önce',
        4320 => '3 gün önce',
        10080 => '1 hafta önce',
    ];

    public const REPEATS = [
        'daily' => 'Her gün',
        'weekdays' => 'Hafta içi her gün',
        'weekly' => 'Her hafta',
        'monthly' => 'Her ay',
    ];

    protected $fillable = [
        'user_id',
        'ticket_id',
        'title',
        'description',
        'priority',
        'status',
        'due_at',
        'remind_before_minutes',
        'repeat',
        'checklist',
        'photos',
    ];

    /** Fotoğraflar kişiye özel olduğu için herkese açık değil, özel diskte tutulur. */
    public const PHOTO_DISK = 'local';

    protected $casts = [
        'due_at' => 'datetime',
        'remind_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminded_at' => 'datetime',
        'overdue_notified_at' => 'datetime',
        'checklist' => 'array',
        'photos' => 'array',
        'remind_before_minutes' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Task $task) {
            $task->remind_at = $task->due_at && $task->remind_before_minutes !== null
                ? $task->due_at->copy()->subMinutes($task->remind_before_minutes)
                : null;

            // Tarih ya da hatırlatma değişince bildirimler yeni zamana göre yeniden gönderilebilsin
            if ($task->isDirty(['due_at', 'remind_before_minutes'])) {
                $task->reminded_at = null;
                $task->overdue_notified_at = null;
            }

            if ($task->isDirty('status')) {
                $task->completed_at = $task->status === 'tamamlandi' ? ($task->completed_at ?? now()) : null;
            }
        });

        // Bildirim işaretleri TaskReminders'ta doğrudan sorguyla yazılır; elimizdeki model
        // bunları görmemiş olabileceğinden sıfırlamayı veritabanında da garanti et
        // Formdan çıkarılan ya da işle birlikte silinen fotoğrafların dosyaları da silinsin
        static::updated(function (Task $task) {
            if ($task->wasChanged('photos')) {
                $old = json_decode((string) $task->getRawOriginal('photos'), true) ?: [];
                static::deletePhotos(array_diff($old, $task->photos ?? []));
            }
        });

        static::deleted(fn (Task $task) => static::deletePhotos($task->photos ?? []));

        static::saved(function (Task $task) {
            if ($task->wasChanged(['due_at', 'remind_before_minutes'])) {
                static::query()->whereKey($task->id)->update(['reminded_at' => null, 'overdue_notified_at' => null]);
            }
        });
    }

    /** @param  array<string>  $paths */
    private static function deletePhotos(array $paths): void
    {
        // Aynı dosyayı başka bir iş kullanıyorsa (kopyalanmış kayıt) dokunma
        $paths = array_filter($paths, fn (string $path) => ! static::query()->whereJsonContains('photos', $path)->exists());

        if ($paths) {
            \Illuminate\Support\Facades\Storage::disk(self::PHOTO_DISK)->delete(array_values($paths));
        }
    }

    public function photoCount(): int
    {
        return count($this->photos ?? []);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scopeOwnedBy(Builder $query, ?User $user): Builder
    {
        return $user ? $query->where('user_id', $user->id) : $query->whereRaw('1 = 0');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', 'tamamlandi');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->open()->whereNotNull('due_at')->where('due_at', '<', now());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->open()->whereBetween('due_at', [now(), now()->endOfDay()]);
    }

    /** Gecikenler önce, sonra bitişi en yakın olan; tarihsizler en sonda. */
    public function scopeByUrgency(Builder $query): Builder
    {
        return $query
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->orderByRaw("CASE priority WHEN 'acil' THEN 0 WHEN 'yüksek' THEN 1 WHEN 'orta' THEN 2 ELSE 3 END");
    }

    public function isCompleted(): bool
    {
        return $this->status === 'tamamlandi';
    }

    public function isOverdue(): bool
    {
        return ! $this->isCompleted() && $this->due_at?->isPast();
    }

    public function isDueToday(): bool
    {
        return ! $this->isCompleted() && $this->due_at?->isToday() && ! $this->isOverdue();
    }

    /** @return array{done: int, total: int}|null */
    public function checklistProgress(): ?array
    {
        $items = collect($this->checklist ?? [])->filter(fn ($item) => filled($item['text'] ?? null));

        return $items->isEmpty() ? null : [
            'done' => $items->where('done', true)->count(),
            'total' => $items->count(),
        ];
    }

    /** Örn. "2 gün kaldı", "3 saat gecikti", "Bugün 15:00". */
    public function dueLabel(): ?string
    {
        if (! $this->due_at) {
            return null;
        }

        if ($this->isOverdue()) {
            return $this->due_at->diffForHumans(now(), CarbonInterface::DIFF_ABSOLUTE).' gecikti';
        }

        if ($this->due_at->isToday()) {
            return 'Bugün '.$this->due_at->format('H:i');
        }

        if ($this->due_at->isTomorrow()) {
            return 'Yarın '.$this->due_at->format('H:i');
        }

        return $this->due_at->translatedFormat('j F D H:i');
    }

    /**
     * İşi tamamlar. Tekrarlanan bir işse bir sonraki tekrarı oluşturur ve onu döner.
     */
    public function complete(): ?self
    {
        return DB::transaction(function () {
            $next = null;

            if ($this->repeat && $this->due_at && ! $this->isCompleted()) {
                // Fotoğraflar o seferin kaydıdır; sonraki tekrara taşınmaz
                $next = $this->replicate(['completed_at', 'reminded_at', 'overdue_notified_at', 'remind_at', 'photos']);
                $next->status = 'bekliyor';
                $next->due_at = $this->nextOccurrence($this->due_at);
                $next->checklist = collect($this->checklist ?? [])
                    ->map(fn ($item) => [...$item, 'done' => false])
                    ->all();
                $next->save();

                // Tekrar zinciri yeni kayıtla sürer; bu kayıt geri açılıp yeniden kapatılırsa kopya oluşmaz
                $this->repeat = null;
            }

            $this->status = 'tamamlandi';
            $this->save();

            return $next;
        });
    }

    public function reopen(): void
    {
        $this->status = 'bekliyor';
        $this->save();
    }

    /** Bitiş tarihini ileri alır; tarihi olmayan işe şimdiden itibaren süre verir. */
    public function snooze(string $interval): void
    {
        $base = $this->due_at && $this->due_at->isFuture() ? $this->due_at->copy() : now();

        $this->due_at = match ($interval) {
            '1h' => $base->addHour(),
            'tomorrow' => now()->addDay()->setTime($this->due_at?->hour ?? 9, $this->due_at?->minute ?? 0),
            'week' => $base->addWeek(),
            default => $base,
        };
        $this->save();
    }

    /** Bir sonraki tekrar; tarih geçmişte kaldıysa gelecekteki ilk tekrara atlar. */
    private function nextOccurrence(Carbon $from): Carbon
    {
        $date = $from->copy();

        do {
            $date = match ($this->repeat) {
                'daily' => $date->addDay(),
                'weekdays' => $date->addWeekday(),
                'weekly' => $date->addWeek(),
                'monthly' => $date->addMonthNoOverflow(),
                default => $date->addDay(),
            };
        } while ($date->isPast());

        return $date;
    }
}
