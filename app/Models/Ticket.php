<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'tracking_number',
        'name',
        'department_room',
        'category_id',
        'subject',
        'description',
        'status',
        'priority',
        'resolution_note',
        'ip_address',
        'computer_name',
        'resolved_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->ip_address = request()->ip();
            
            if ($ticket->ip_address) {
                // Suppress errors for gethostbyaddr if IP is invalid or unreachable
                $ticket->computer_name = @gethostbyaddr($ticket->ip_address);
                
                // If gethostbyaddr fails and returns false, fall back to IP
                if ($ticket->computer_name === false) {
                    $ticket->computer_name = $ticket->ip_address;
                }
            }
        });
    }
}