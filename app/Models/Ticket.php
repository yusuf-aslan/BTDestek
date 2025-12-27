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
        'ip_address',
        'computer_name',
        'resolved_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}