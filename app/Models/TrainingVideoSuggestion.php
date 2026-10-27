<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingVideoSuggestion extends Model
{
    public const STATUSES = [
        'pending' => 'Bekliyor',
        'in_progress' => 'Hazırlanıyor',
        'published' => 'Yayınlandı',
        'rejected' => 'Reddedildi',
    ];

    public const STATUS_COLORS = [
        'pending' => 'warning',
        'in_progress' => 'info',
        'published' => 'success',
        'rejected' => 'gray',
    ];

    /** Yeni gelen aynı öneri bu durumlardaki kayda eklenir; kapanmış kayıtlar yeniden açılmaz. */
    public const OPEN_STATUSES = ['pending', 'in_progress'];

    protected $fillable = [
        'training_video_category_id',
        'topic',
        'details',
        'name',
        'department',
        'ip_address',
        'status',
        'request_count',
        'training_video_id',
        'admin_note',
        'last_requested_at',
    ];

    protected $casts = [
        'request_count' => 'integer',
        'last_requested_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (TrainingVideoSuggestion $suggestion) {
            $suggestion->topic_key = static::topicKey((string) $suggestion->topic);
            $suggestion->last_requested_at ??= now();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TrainingVideoCategory::class, 'training_video_category_id');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(TrainingVideo::class, 'training_video_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    /** Büyük/küçük harf, noktalama ve fazla boşluk farkı aynı öneri sayılır. */
    public static function topicKey(string $topic): string
    {
        $key = mb_strtolower(str_replace(['I', 'İ'], ['ı', 'i'], $topic), 'UTF-8');
        $key = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $key);

        return mb_substr(trim(preg_replace('/\s+/', ' ', $key)), 0, 255);
    }

    /** Aynı modülde açık bir eşi varsa onun sayısını artırır, yoksa yeni öneri açar. */
    public static function submit(array $data): self
    {
        $existing = static::query()
            ->open()
            ->where('topic_key', static::topicKey($data['topic']))
            ->where('training_video_category_id', $data['training_video_category_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->increment('request_count', 1, ['last_requested_at' => now()]);

            return $existing;
        }

        return static::create($data + ['status' => 'pending']);
    }
}
