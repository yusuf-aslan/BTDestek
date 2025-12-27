<?php

use Illuminate\Support\Facades\Schema;

test('departments table has expected columns', function () {
    expect(Schema::hasTable('departments'))->toBeTrue();
    expect(Schema::hasColumns('departments', [
        'id', 'name', 'created_at', 'updated_at'
    ]))->toBeTrue();
});

test('categories table has expected columns', function () {
    expect(Schema::hasTable('categories'))->toBeTrue();
    expect(Schema::hasColumns('categories', [
        'id', 'name', 'description', 'created_at', 'updated_at'
    ]))->toBeTrue();
});

test('tickets table has expected columns', function () {
    expect(Schema::hasTable('tickets'))->toBeTrue();
    expect(Schema::hasColumns('tickets', [
        'id', 
        'tracking_number', 
        'name', 
        'department_room', 
        'category_id', 
        'subject', 
        'description', 
        'status', 
        'priority', 
        'ip_address', 
        'computer_name', 
        'resolved_at', 
        'created_at', 
        'updated_at'
    ]))->toBeTrue();
});

test('announcements table has expected columns', function () {
    expect(Schema::hasTable('announcements'))->toBeTrue();
    expect(Schema::hasColumns('announcements', [
        'id', 'title', 'content', 'type', 'is_active', 'created_at', 'updated_at'
    ]))->toBeTrue();
});

test('canned_responses table has expected columns', function () {
    expect(Schema::hasTable('canned_responses'))->toBeTrue();
    expect(Schema::hasColumns('canned_responses', [
        'id', 'title', 'content', 'created_at', 'updated_at'
    ]))->toBeTrue();
});
