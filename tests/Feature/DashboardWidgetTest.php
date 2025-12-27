<?php

use App\Filament\Widgets\ActiveAnnouncements;
use App\Models\User;
use function Pest\Laravel\actingAs;

test('dashboard has active announcements widget', function () {
    $user = User::factory()->create();
    
    // Just verify the class exists for now as failing to import handles the "Red" phase
    expect(class_exists(ActiveAnnouncements::class))->toBeTrue();
});