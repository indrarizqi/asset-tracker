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
        // Akun Super Admin
        \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@vodeco.id',
            'role' => 'super_admin',
            'password' => bcrypt('superpassword'),
        ]);

        // Akun Admin Biasa
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@vodeco.id',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
    }
}
