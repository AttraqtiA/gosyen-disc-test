<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Position;
use App\Models\PositionDiscProfile;
use App\Models\PositionMbtiProfile;
use App\Models\TestSession;
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
                    ['description' => $item['description'], 'is_active' => true, 'is_global' => true]
                );

                $position->clients()->syncWithoutDetaching([$client->id]);

                PositionDiscProfile::updateOrCreate(
                    ['position_id' => $position->id],
                    [...$item['profile'], 'test_type' => 'DISC', 'is_active' => true]
                );
            }

            $mbtiPositions = [
                [
                    'title' => 'Business Development',
                    'description' => 'Peran ekspansi pasar, relasi, dan inisiatif peluang.',
                    'profile' => ['e_target' => 75, 'i_target' => 25, 's_target' => 40, 'n_target' => 60, 't_target' => 55, 'f_target' => 45, 'j_target' => 55, 'p_target' => 45],
                ],
                [
                    'title' => 'Finance & Accounting Analyst',
                    'description' => 'Peran analitik, ketelitian angka, dan kepatuhan prosedur.',
                    'profile' => ['e_target' => 35, 'i_target' => 65, 's_target' => 70, 'n_target' => 30, 't_target' => 70, 'f_target' => 30, 'j_target' => 70, 'p_target' => 30],
                ],
                [
                    'title' => 'HR Business Partner',
                    'description' => 'Peran people strategy, komunikasi lintas fungsi, dan mediasi.',
                    'profile' => ['e_target' => 60, 'i_target' => 40, 's_target' => 45, 'n_target' => 55, 't_target' => 40, 'f_target' => 60, 'j_target' => 55, 'p_target' => 45],
                ],
            ];

            foreach ($mbtiPositions as $item) {
                $position = Position::updateOrCreate(
                    ['title' => $item['title']],
                    ['client_id' => $client->id, 'description' => $item['description'], 'is_active' => true, 'is_global' => true]
                );

                $position->clients()->syncWithoutDetaching([$client->id]);

                PositionMbtiProfile::updateOrCreate(
                    ['position_id' => $position->id, 'test_type' => 'MBTI'],
                    [...$item['profile'], 'notes' => null, 'is_active' => true]
                );
            }

            TestSession::updateOrCreate(
                ['code' => 'DEMODISC'],
                [
                    'name' => 'Demo DISC Session',
                    'client_id' => $client->id,
                    'test_type' => 'DISC',
                    'is_active' => true,
                    'expires_at' => null,
                ]
            );

            TestSession::updateOrCreate(
                ['code' => 'DEMOMBTI'],
                [
                    'name' => 'Demo MBTI Session',
                    'client_id' => $client->id,
                    'test_type' => 'MBTI',
                    'is_active' => true,
                    'expires_at' => null,
                ]
            );
        });
    }
}
