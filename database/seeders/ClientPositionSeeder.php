<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Position;
use App\Models\PositionDiscProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $client = Client::firstOrCreate(
                ['code' => 'default-client'],
                ['name' => 'Default Client', 'is_active' => true]
            );

            $positions = [
                [
                    'title' => 'Sales Executive',
                    'description' => 'Peran dengan kebutuhan persuasi dan orientasi hasil.',
                    'profile' => ['d_target' => 30, 'i_target' => 40, 's_target' => 15, 'c_target' => 15],
                ],
                [
                    'title' => 'Project Coordinator',
                    'description' => 'Peran koordinasi tim dan kestabilan proses.',
                    'profile' => ['d_target' => 20, 'i_target' => 20, 's_target' => 35, 'c_target' => 25],
                ],
                [
                    'title' => 'Quality Assurance Analyst',
                    'description' => 'Peran detail, akurasi, dan kepatuhan proses.',
                    'profile' => ['d_target' => 15, 'i_target' => 10, 's_target' => 30, 'c_target' => 45],
                ],
            ];

            foreach ($positions as $item) {
                $position = Position::updateOrCreate(
                    ['client_id' => $client->id, 'title' => $item['title']],
                    ['description' => $item['description'], 'is_active' => true]
                );

                PositionDiscProfile::updateOrCreate(
                    ['position_id' => $position->id],
                    [...$item['profile'], 'is_active' => true]
                );
            }
        });
    }
}
