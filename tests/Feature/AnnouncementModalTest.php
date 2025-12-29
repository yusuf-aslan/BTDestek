<?php

use App\Models\Announcement;

test('announcement modal is present when active announcements exist', function () {
    Announcement::create([
        'title' => 'Emergency Update',
        'content' => 'Server restart in 5 mins',
        'type' => 'danger',
        'is_active' => true
    ]);

    $this->get('/')
        ->assertSuccessful()
        // Check for Alpine.js initialization
        ->assertSee('x-data="{ show: true }"', false)
        // Check for Modal Title
        ->assertSee('Önemli Duyurular')
        // Check for Announcement Content inside Modal
        ->assertSee('Emergency Update')
        ->assertSee('Server restart in 5 mins')
        // Check for Close Button
        ->assertSee('Okudum, Anladım');
});

test('announcement modal is NOT present when no active announcements exist', function () {
    // Ensure no active announcements
    Announcement::query()->delete();

    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee('x-data="{ show: true }"', false)
        ->assertDontSee('Önemli Duyurular');
});
