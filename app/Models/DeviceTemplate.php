<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceTemplate extends Model
{
    protected $fillable = ['name', 'specs', 'model'];

    protected $casts = [
        'specs' => 'array',
    ];
}
