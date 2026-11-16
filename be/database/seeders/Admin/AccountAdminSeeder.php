<?php

namespace Database\Seeders\Admin;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'trucquy@gmail.com'],
            [
                'name' => 'trucquy',
                'password' => Hash::make('admin@123'),
            ]
        );
    }
}

