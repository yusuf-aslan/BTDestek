<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'site_title',
        'site_logo',
        'site_favicon',
        'important_reminders',
        'work_hours_start',
        'work_hours_end',
        'allow_tickets_outside_work_hours',
        'weekend_tickets_allowed',
        'is_dark_mode_enabled',
        'show_email_on_ticket_form',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    protected $casts = [
        'important_reminders' => 'array',
        'allow_tickets_outside_work_hours' => 'boolean',
        'weekend_tickets_allowed' => 'boolean',
        'is_dark_mode_enabled' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('general_settings');
        });
    }
}