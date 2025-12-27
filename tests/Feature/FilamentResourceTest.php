<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can render department resource', function () {
    actingAs($this->user)
        ->get(\App\Filament\Resources\Departments\DepartmentResource::getUrl('index'))
        ->assertSuccessful();
});

test('can render category resource', function () {
    actingAs($this->user)
        ->get(\App\Filament\Resources\Categories\CategoryResource::getUrl('index'))
        ->assertSuccessful();
});

test('can render announcement resource', function () {
    actingAs($this->user)
        ->get(\App\Filament\Resources\Announcements\AnnouncementResource::getUrl('index'))
        ->assertSuccessful();
});

test('can render canned response resource', function () {
    actingAs($this->user)
        ->get(\App\Filament\Resources\CannedResponses\CannedResponseResource::getUrl('index'))
        ->assertSuccessful();
});