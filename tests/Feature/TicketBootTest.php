<?php

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\Request;

test('ticket captures ip address and computer name on creation', function () {
    // Bind a fake request to the container
    $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '10.0.0.5']);
    app()->instance('request', $request);
    
    // Create a Category first
    $category = Category::create(['name' => 'General']);

    // Create Ticket without specifying IP or computer name
    $ticket = Ticket::create([
        'tracking_number' => 'AUTO-2',
        'name' => 'Auto User',
        'department_room' => 'Room 2',
        'category_id' => $category->id,
        'subject' => 'Auto Test',
        'description' => 'Testing boot logic',
    ]);

    expect($ticket->ip_address)->toBe('10.0.0.5');
    // computer_name logic depends on gethostbyaddr('10.0.0.5'). 
    // It will likely return the IP itself if DNS fails, or a hostname.
    expect($ticket->computer_name)->not->toBeNull();
});