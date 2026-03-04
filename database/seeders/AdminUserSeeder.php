<?php

namespace Database\Seeders;

use App\Models\Client;
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

        $defaultClient = Client::firstOrCreate(
            ['code' => 'default-client'],
            ['name' => 'Default Client', 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'secure.admin.gosyen.2026@protonmail.com'],
            [
                'name' => 'Admin Gosyen',
                'password' => Hash::make('G0sy3n!Adm1n#2026$kQ7'),
                'role' => 'superadmin',
                'client_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'client.admin.default.2026@protonmail.com'],
            [
                'name' => 'Client Admin Default',
                'password' => Hash::make('Cl13nt@Adm1n#2026!X9'),
                'role' => 'client_admin',
                'client_id' => $defaultClient->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'reviewer.default.2026@protonmail.com'],
            [
                'name' => 'Reviewer Default',
                'password' => Hash::make('R3v13w3r!2026#Qm8'),
                'role' => 'reviewer',
                'client_id' => $defaultClient->id,
            ]
        );
    }
}
