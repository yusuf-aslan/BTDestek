<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'specs' => 'array',
        'purchase_date' => 'date',
        'warranty_expires_at' => 'date',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function deviceTemplate(): BelongsTo
    {
        return $this->belongsTo(DeviceTemplate::class);
    }



    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Virtual Mutator for Import: RAM
     * Merges the 'ram' value into the 'specs' JSON column.
     */
    public function setRamAttribute($value)
    {
        $specs = $this->specs ?? [];
        $specs['ram'] = $value;
        $this->attributes['specs'] = json_encode($specs);
    }

    /**
     * Virtual Mutator for Import: Monitor
     * Merges the 'monitor' value into the 'specs' JSON column.
     */
    public function setMonitorAttribute($value)
    {
        $specs = $this->specs ?? [];
        $specs['monitor'] = $value;
        $this->attributes['specs'] = json_encode($specs);
    }
}