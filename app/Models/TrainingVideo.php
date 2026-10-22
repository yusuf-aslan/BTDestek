<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrainingVideo extends Model
{
    // Video ve altyazılar dışarıdan erişilemeyen diskte tutulur, controller üzerinden yetki kontrolüyle sunulur
    public const FILE_DISK = 'local';

    public const POSTER_DISK = 'public';

    public const DIRECTORY = 'training-videos';

    public const AUDIENCES = [
        'public' => 'Herkes (destek sayfası)',
        'staff' => 'Yalnızca personel (giriş yapmış kullanıcılar)',
    ];

    public const SOURCE_TYPES = [
        'upload' => 'Dosya yükle',
        'url' => 'Bağlantı (YouTube, Vimeo veya doğrudan video adresi)',
    ];

    protected $fillable = [
        'title',
        'slug',
        'topic',
        'description',
        'audience',
        'source_type',
        'video_path',
        'video_url',
        'poster_path',
        'subtitle_path',
        'is_published',
        'sort_order',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'views' => 'integer',
    ];

    public static function canManage(?User $user): bool
    {
        return (bool) $user?->hasModuleAccess('training_videos');
    }

    /**
     * Yöneticiler taslaklar dahil tüm videoları, giriş yapmış kullanıcılar personel videolarını,
     * ziyaretçiler yalnızca herkese açık yayındaki videoları görür.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (static::canManage($user)) {
            return $query;
        }

        return $query
            ->where('is_published', true)
            ->when(! $user, fn (Builder $q) => $q->where('audience', 'public'));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function isVisibleTo(?User $user): bool
    {
        if (static::canManage($user)) {
            return true;
        }

        return $this->is_published && ($user || $this->audience === 'public');
    }

    public function isUpload(): bool
    {
        return $this->source_type === 'upload';
    }

    public function hasPlayableSource(): bool
    {
        return $this->isUpload() ? filled($this->video_path) : filled($this->video_url);
    }

    /**
     * YouTube / Vimeo bağlantıları için gömme adresi; doğrudan video bağlantılarında null döner.
     */
    public function embedUrl(): ?string
    {
        if ($this->isUpload() || blank($this->video_url)) {
            return null;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $this->video_url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1].'?rel=0';
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $this->video_url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return null;
    }

    public function videoSourceUrl(): ?string
    {
        if ($this->isUpload()) {
            return filled($this->video_path) ? route('training.video', $this) : null;
        }

        return $this->embedUrl() ? null : $this->video_url;
    }

    public function posterUrl(): ?string
    {
        return filled($this->poster_path) ? Storage::disk(self::POSTER_DISK)->url($this->poster_path) : null;
    }

    public function subtitleUrl(): ?string
    {
        return filled($this->subtitle_path) ? route('training.subtitle', $this) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (TrainingVideo $video) {
            // Kaynak türü değişince diğer türün verisini temizle (eski dosya aşağıda silinir)
            if ($video->isUpload()) {
                $video->video_url = null;
            } else {
                $video->video_path = null;
            }
        });

        static::updated(function (TrainingVideo $video) {
            foreach (['video_path' => self::FILE_DISK, 'subtitle_path' => self::FILE_DISK, 'poster_path' => self::POSTER_DISK] as $attribute => $disk) {
                $old = $video->getOriginal($attribute);

                if ($video->wasChanged($attribute) && filled($old)) {
                    Storage::disk($disk)->delete($old);
                }
            }
        });

        static::deleted(function (TrainingVideo $video) {
            foreach (['video_path' => self::FILE_DISK, 'subtitle_path' => self::FILE_DISK, 'poster_path' => self::POSTER_DISK] as $attribute => $disk) {
                if (filled($video->{$attribute})) {
                    Storage::disk($disk)->delete($video->{$attribute});
                }
            }
        });
    }
}
