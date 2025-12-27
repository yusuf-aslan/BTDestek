<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Departments
        $departments = [
            'Yazılım',
            'Donanım',
            'Network',
            'Tıbbi Cihaz',
            'İdari İşler',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name]);
        }

        // Seed Categories
        $categories = [
            ['name' => 'Yazıcı', 'description' => 'Yazıcı ve tarayıcı sorunları'],
            ['name' => 'Bilgisayar', 'description' => 'Bilgisayar donanım ve yavaşlama sorunları'],
            ['name' => 'İnternet', 'description' => 'İnternet bağlantı ve wifi sorunları'],
            ['name' => 'HBYS', 'description' => 'Hastane Bilgi Yönetim Sistemi sorunları'],
            ['name' => 'E-Posta', 'description' => 'E-posta kurulum ve gönderim sorunları'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], ['description' => $category['description']]);
        }

        // Seed default user if not exists
        if (!User::where('email', 'admin@btdestek.com')->exists()) {
             User::factory()->create([
                'name' => 'Admin User',
                'email' => 'webvetasarim@gmail.com',
                'password' => bcrypt('1156'),
            ]);
        }
    }
}
