<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@local.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Empresa X',
            'email' => 'empresa@local.test',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        User::create([
            'name' => 'Freela Y',
            'email' => 'freela@local.test',
            'password' => Hash::make('password'),
            'role' => 'freelancer',
        ]);
    }
}
