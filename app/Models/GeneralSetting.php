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
        'is_lunch_break_enabled',
        'lunch_break_start',
        'lunch_break_end',
        'is_dark_mode_enabled',
        'show_email_on_ticket_form',
        'show_broken_pc_ip_on_ticket_form',
        'ip_display_position',
        'remote_support_download_url',
        'remote_support_guide_url',
        'remote_support_title',
        'menu_layout',
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
        'is_lunch_break_enabled' => 'boolean',
        'is_dark_mode_enabled' => 'boolean',
        'show_email_on_ticket_form' => 'boolean',
        'show_broken_pc_ip_on_ticket_form' => 'boolean',
    ];

    public function isLunchBreakAt($dateTime = null): bool
    {
        if (! $this->is_lunch_break_enabled || ! $this->lunch_break_start || ! $this->lunch_break_end) {
            return false;
        }

        $moment = $dateTime ? \Carbon\Carbon::parse($dateTime) : now();
        $start = $moment->copy()->setTimeFromTimeString($this->lunch_break_start);
        $end = $moment->copy()->setTimeFromTimeString($this->lunch_break_end);

        return $end->greaterThan($start)
            && $moment->greaterThanOrEqualTo($start)
            && $moment->lessThan($end);
    }

    public function lunchBreakEndsAt($dateTime = null): \Carbon\Carbon
    {
        $moment = $dateTime ? \Carbon\Carbon::parse($dateTime) : now();

        return $moment->copy()->setTimeFromTimeString($this->lunch_break_end ?: '13:00:00');
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('general_settings');
        });
    }
}
