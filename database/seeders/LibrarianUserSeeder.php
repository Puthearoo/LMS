<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LibrarianUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'librarian',
            'email' => 'librarian@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('11111111'),
            'contact' => '+1234567890',
            'role' => 'librarian',
        ]);
    }
}