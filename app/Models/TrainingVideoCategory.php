<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Eğitim videolarının gruplandığı modüller (ör. Talep Oluşturma, Stok, Personel).
 */
class TrainingVideoCategory extends Model
{
    public const DEFAULT_COLOR = '#2563eb';

    public const PALETTE = ['#2563eb', '#16a34a', '#dc2626', '#9333ea', '#d97706', '#0891b2', '#db2777', '#65a30d', '#4f46e5', '#ea580c'];

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function videos(): HasMany
    {
        return $this->hasMany(TrainingVideo::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Sayfada style niteliğine yazıldığı için yalnızca geçerli hex renk döner.
     */
    public function dotColor(): string
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $this->color) ? $this->color : self::DEFAULT_COLOR;
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'modul';
        $slug = $base;

        for ($n = 2; static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $n++) {
            $slug = $base.'-'.$n;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        // Video formundan hızlı eklenen modüllerde slug ve renk otomatik atanır
        static::creating(function (TrainingVideoCategory $category) {
            if (blank($category->slug)) {
                $category->slug = static::uniqueSlug($category->name);
            }

            if (blank($category->color)) {
                $category->color = self::PALETTE[static::query()->count() % count(self::PALETTE)];
            }

            if ($category->sort_order === null) {
                $category->sort_order = (int) static::query()->max('sort_order') + 1;
            }
        });
    }
}
