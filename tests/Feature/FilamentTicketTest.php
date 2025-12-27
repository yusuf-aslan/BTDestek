<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can render ticket resource', function () {
    actingAs($this->user)
        ->get(\App\Filament\Resources\Tickets\TicketResource::getUrl('index'))
        ->assertSuccessful();
});

test('can render create ticket page', function () {
    actingAs($this->user)
        ->get(\App\Filament\Resources\Tickets\TicketResource::getUrl('create'))
        ->assertSuccessful();
});
