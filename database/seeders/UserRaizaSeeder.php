<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRaizaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if user exists to avoid duplicates
        if (!User::where('email', 'raizacordero@gmail.com')->exists()) {
            User::create([
                'name' => 'Raiza Cordero',
                'email' => 'raizacordero@gmail.com',
                'password' => Hash::make('password'),
            ]);
            $this->command->info('User Raiza Cordero created successfully.');
        } else {
            $this->command->info('User Raiza Cordero already exists.');
        }
    }
}
