<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@mtsmuhamadyah.sch.id',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Create librarian user
        User::create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'petugas@mtsmuhamadyah.sch.id',
            'password' => Hash::make('petugas123'),
            'email_verified_at' => now(),
        ]);
    }
}