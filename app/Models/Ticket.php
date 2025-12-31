<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'tracking_number',
        'name',
        'department_room',
        'phone_number',
        'category_id',
        'subject',
        'description',
        'status',
        'priority',
        'resolution_note',
        'ip_address',
        'resolved_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->ip_address = request()->ip();
        });
    }
}
