<?php

use App\Models\Ticket;
use App\Models\GeneralSetting;
use Carbon\Carbon;

function lunchBreakSettings(bool $enabled = true): GeneralSetting
{
    return new GeneralSetting([
        'is_lunch_break_enabled' => $enabled,
        'lunch_break_start' => '12:00:00',
        'lunch_break_end' => '13:00:00',
    ]);
}

test('a ticket opened during lunch starts counting at 13:00', function () {
    $ticket = new Ticket();
    $ticket->setDateFormat('Y-m-d H:i:s');
    $ticket->created_at = Carbon::parse('2026-08-05 12:20:00');

    $settings = lunchBreakSettings();

    expect($ticket->elapsedMinutes(Carbon::parse('2026-08-05 12:55:00'), $settings))->toBe(0)
        ->and($ticket->elapsedMinutes(Carbon::parse('2026-08-05 13:15:00'), $settings))->toBe(15);
});

test('a ticket opened outside lunch starts counting immediately', function () {
    $ticket = new Ticket();
    $ticket->setDateFormat('Y-m-d H:i:s');
    $ticket->created_at = Carbon::parse('2026-08-05 11:40:00');

    expect($ticket->elapsedMinutes(Carbon::parse('2026-08-05 12:10:00'), lunchBreakSettings()))->toBe(30);
});

test('lunch rule can be disabled', function () {
    $ticket = new Ticket();
    $ticket->setDateFormat('Y-m-d H:i:s');
    $ticket->created_at = Carbon::parse('2026-08-05 12:20:00');

    expect($ticket->elapsedMinutes(
        Carbon::parse('2026-08-05 12:55:00'),
        lunchBreakSettings(false)
    ))->toBe(35);
});
