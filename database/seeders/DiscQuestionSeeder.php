<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DiscQuestion;
use App\Models\DiscStatement;

class DiscQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DiscQuestion::create([
            'question_number' => 1
        ]);

        DiscStatement::insert([
            [
                'disc_question_id' => 1,
                'text' => 'Gampang gaul, Mudah setuju',
                'disc_type' => 'I'
            ],

        ]);
    }
}
