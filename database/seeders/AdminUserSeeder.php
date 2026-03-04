<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('email', 'admin@gosyen.local')->delete();

        User::updateOrCreate(
            ['email' => 'admingosyen@gmail.com'],
            [
                'name' => 'Admin Gosyen',
                'password' => Hash::make('admin12345'),
            ]
        );
    }
}
