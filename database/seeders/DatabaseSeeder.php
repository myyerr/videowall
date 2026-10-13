<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Display;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default admin user
        User::firstOrCreate(
            ['email' => 'admin@visionwall.local'],
            [
                'name'     => 'Admin VisionWall',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Sample display
        Display::firstOrCreate(
            ['name' => 'Videowall Lobby Utama'],
            [
                'location'     => 'Lobby Lt. 1',
                'device_token' => Str::random(32),
                'status'       => 'active',
            ]
        );
    }
}
