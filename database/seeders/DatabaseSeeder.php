<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Super Admin
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@vodeco.co.id',
            'role' => 'super_admin',
            'password' => bcrypt('super-123'),
        ]);

        // Akun Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@vodeco.co.id',
            'role' => 'admin',
            'password' => bcrypt('admin-123'),
        ]);
    }
}
