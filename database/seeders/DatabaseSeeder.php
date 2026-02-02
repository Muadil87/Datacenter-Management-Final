<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@manager.com',
            'password' => bcrypt('manager123'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Internal User',
            'email' => 'internal@internal.com',
            'password' => bcrypt('internal123'),
            'role' => 'internal',
            'is_active' => true,
        ]);
    }
}
