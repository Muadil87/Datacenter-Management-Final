<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'administrateur
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@datacenter.com',
            'password' => Hash::make('password'), // Mot de passe: password
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Créer un utilisateur test
        User::create([
            'name' => 'Utilisateur Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role' => 'internal',
            'is_active' => true,
        ]);
    }
}
