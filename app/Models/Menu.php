<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'title',
        'url',
        'parent_id',
        'target',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

        public function children(): HasMany

        {

            return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');

        }

    

        protected static function booted()

        {

            static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('public_menus'));

            static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('public_menus'));

        }

    }

    