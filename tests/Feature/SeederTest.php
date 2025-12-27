<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

test('database seeder seeds departments and categories', function () {
    $this->seed(DatabaseSeeder::class);

    expect(DB::table('departments')->where('name', 'Yazılım')->exists())->toBeTrue();
    expect(DB::table('departments')->where('name', 'Donanım')->exists())->toBeTrue();
    
    expect(DB::table('categories')->where('name', 'Yazıcı')->exists())->toBeTrue();
    expect(DB::table('categories')->where('name', 'İnternet')->exists())->toBeTrue();
});
