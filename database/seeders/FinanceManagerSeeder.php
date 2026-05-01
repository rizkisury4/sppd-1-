<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinanceManagerSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'finance@example.com'],
            [
                'name' => 'Manager Keuangan',
                'password' => Hash::make('password'),
                'role' => 'finance',
            ]
        );
    }
}
