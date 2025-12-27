<?php

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\Request;

test('ticket model has phone_number and not computer_name', function () {
    $category = Category::create(['name' => 'Test']);
    
    $ticket = Ticket::create([
        'tracking_number' => 'PHONE-1',
        'name' => 'User',
        'department_room' => 'Room 1',
        'phone_number' => '1234',
        'category_id' => $category->id,
        'subject' => 'Test',
        'description' => 'Test',
    ]);

    expect($ticket->phone_number)->toBe('1234');
    expect($ticket->computer_name)->toBeNull();
});

test('booted logic does not capture computer name', function () {
    $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '1.1.1.1']);
    app()->instance('request', $request);

    $category = Category::create(['name' => 'Test']);
    
    $ticket = Ticket::create([
        'tracking_number' => 'PHONE-2',
        'name' => 'User',
        'department_room' => 'Room 1',
        'phone_number' => '5555',
        'category_id' => $category->id,
        'subject' => 'Test',
        'description' => 'Test',
    ]);

    expect($ticket->ip_address)->toBe('1.1.1.1');
    expect($ticket->computer_name)->toBeNull();
});
