<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'     => 'ADMIN SUPER',
                'password' => Hash::make('password123'),
                'role'     => 'superadmin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'adminstok@gmail.com'],
            [
                'name'     => 'Admin Gudang',
                'password' => Hash::make('password123'),
                'role'     => 'admin_stationery',
            ]
        );
    }
}
