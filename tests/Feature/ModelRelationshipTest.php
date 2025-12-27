<?php

use App\Models\Department;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Announcement;
use App\Models\CannedResponse;

test('department model is fillable', function () {
    $department = Department::create(['name' => 'IT']);
    expect($department->name)->toBe('IT');
});

test('category model is fillable', function () {
    $category = Category::create(['name' => 'Printers', 'description' => 'All printer issues']);
    expect($category->name)->toBe('Printers');
    expect($category->description)->toBe('All printer issues');
});

test('ticket model is fillable', function () {
    $category = Category::create(['name' => 'General']);
    
    $ticket = Ticket::create([
        'tracking_number' => 'TEST-123',
        'name' => 'John Doe',
        'department_room' => 'Room 101',
        'category_id' => $category->id,
        'subject' => 'Help',
        'description' => 'Issue',
        'status' => 'yeni',
        'priority' => 'orta',
        'ip_address' => '127.0.0.1',
        'computer_name' => 'PC-01'
    ]);

    expect($ticket->tracking_number)->toBe('TEST-123');
    expect($ticket->name)->toBe('John Doe');
});

test('announcement model is fillable', function () {
    $announcement = Announcement::create([
        'title' => 'Alert', 
        'content' => 'System Down',
        'type' => 'danger',
        'is_active' => true
    ]);
    expect($announcement->title)->toBe('Alert');
});

test('canned_response model is fillable', function () {
    $response = CannedResponse::create([
        'title' => 'Reply', 
        'content' => 'Hello'
    ]);
    expect($response->title)->toBe('Reply');
});

test('ticket belongs to category', function () {
    $category = Category::create(['name' => 'Network']);
    $ticket = Ticket::create([
        'tracking_number' => 'NET-1',
        'name' => 'Jane',
        'department_room' => 'IT',
        'category_id' => $category->id,
        'subject' => 'Wifi',
        'description' => 'No signal'
    ]);

    expect($ticket->category)->toBeInstanceOf(Category::class);
    expect($ticket->category->id)->toBe($category->id);
});

test('category has many tickets', function () {
    $category = Category::create(['name' => 'Software']);
    $ticket1 = Ticket::create([
        'tracking_number' => 'SOFT-1',
        'name' => 'User 1',
        'department_room' => 'A',
        'category_id' => $category->id,
        'subject' => 'Bug',
        'description' => 'Error'
    ]);
    $ticket2 = Ticket::create([
        'tracking_number' => 'SOFT-2',
        'name' => 'User 2',
        'department_room' => 'B',
        'category_id' => $category->id,
        'subject' => 'Crash',
        'description' => 'Blue screen'
    ]);

    expect($category->tickets)->toHaveCount(2);
});
