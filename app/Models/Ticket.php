<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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
        'resolved_by',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->ip_address = request()->ip();
        });

        static::updating(function (Ticket $ticket) {
            // If status changed to 'çözüldü', set resolved_by and resolved_at if not set
            if ($ticket->isDirty('status') && $ticket->status === 'çözüldü') {
                if (!$ticket->resolved_at) {
                    $ticket->resolved_at = now();
                }
                $ticket->resolved_by = Auth::id();
            }

            // If resolved_at is provided (from DatePicker), automatically append current time
            if ($ticket->isDirty('resolved_at') && $ticket->resolved_at) {
                $ticket->resolved_at = \Carbon\Carbon::parse($ticket->resolved_at)->setTime(now()->hour, now()->minute, now()->second);
            }
        });
    }
}