<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Create a default admin account so the system can be managed.
     */
    public function run(): void
    {
        $email = 'admin@trampix.com';
        $name = 'Administrador';

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => 'admin',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
            ]
        );
    }
}