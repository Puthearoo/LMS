<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //      User::create([
    //         'name' => 'john',
    //         'email' => 'john@library.com', // Changed to unique
    //         'email_verified_at' => now(),
    //         'password' => Hash::make('11111111'),
    //         'contact' => '+1234567890',
    //         'role' => 'librarian',
    //     ]);

    //     User::create([
    //         'name' => 'wick',
    //         'email' => 'wick@library.com', // Changed to unique
    //         'email_verified_at' => now(),
    //         'password' => Hash::make('11111111'),
    //         'contact' => '+0987654321',
    //         'role' => 'librarian',
    //     ]);
}
