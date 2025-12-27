<?php

use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::create(['name' => 'Test']);
});

test('ticket form has phone_number and not computer_name', function () {
    $ticket = Ticket::create([
        'tracking_number' => 'FIL-1',
        'name' => 'User',
        'department_room' => 'Room',
        'phone_number' => '123',
        'category_id' => $this->category->id,
        'subject' => 'Sub',
        'description' => 'Desc',
    ]);

    $this->actingAs($this->user);

    Livewire::test(EditTicket::class, ['record' => $ticket->id])
        ->assertSee('Dahili No / Tel')
        ->assertDontSee('Bilgisayar Adı');
});
