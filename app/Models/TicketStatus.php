<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class TicketStatus extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'name', 'color', 'destination', 'closes_ticket', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['closes_ticket' => 'boolean', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (TicketStatus $status): void {
            if ($status->closes_ticket && $status->destination === 'pending') {
                $status->destination = 'solved';
            }
        });
        static::saved(fn () => static::forgetCache());
        static::deleted(fn () => static::forgetCache());
        static::deleting(function (TicketStatus $status): void {
            if (Ticket::where('status', $status->key)->exists()) {
                throw ValidationException::withMessages(['key' => 'Bu durum mevcut taleplerde kullanılıyor. Silmek yerine pasif yapın.']);
            }
        });
    }

    public static function allCached(): Collection
    {
        return Cache::rememberForever('ticket_statuses.all', fn () => static::query()->orderBy('sort_order')->orderBy('name')->get());
    }

    public static function forgetCache(): void { Cache::forget('ticket_statuses.all'); }
    public static function options(bool $activeOnly = true): array { return static::allCached()->when($activeOnly, fn (Collection $c) => $c->where('is_active', true))->pluck('name', 'key')->all(); }
    public static function keysFor(string $destination): array { return static::allCached()->where('destination', $destination)->pluck('key')->all(); }
    public static function closedKeys(): array { return static::allCached()->where('closes_ticket', true)->pluck('key')->all(); }
    public static function isClosed(?string $key): bool { return static::allCached()->firstWhere('key', $key)?->closes_ticket ?? false; }
    public static function isSolved(?string $key): bool { return static::allCached()->firstWhere('key', $key)?->destination === 'solved'; }
    public static function colorFor(?string $key): string { return static::allCached()->firstWhere('key', $key)?->color ?? 'gray'; }
    public static function labelFor(?string $key): string { return static::allCached()->firstWhere('key', $key)?->name ?? (string) $key; }
    public static function resolutionKey(): string { return static::allCached()->first(fn ($s) => $s->destination === 'solved' && $s->key === 'çözüldü')?->key ?? static::keysFor('solved')[0] ?? 'çözüldü'; }
    public static function badgeClassesFor(?string $key): string
    {
        return match (static::colorFor($key)) {
            'primary' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
            'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-900/60 dark:text-slate-300',
        };
    }
}
