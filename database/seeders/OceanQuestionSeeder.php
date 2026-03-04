<?php

namespace Database\Seeders;

use App\Models\OceanQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OceanQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['Saya menikmati mencoba pendekatan baru dalam pekerjaan.', 'O', false],
            ['Saya tertarik mempelajari ide atau konsep yang berbeda dari kebiasaan.', 'O', false],
            ['Saya lebih nyaman dengan rutinitas daripada eksperimen.', 'O', true],
            ['Saya suka mengeksplorasi perspektif yang tidak biasa.', 'O', false],
            ['Saya jarang tertarik pada topik di luar bidang saya.', 'O', true],

            ['Saya menyelesaikan tugas tepat waktu sesuai rencana.', 'C', false],
            ['Saya bekerja dengan teliti meskipun detailnya banyak.', 'C', false],
            ['Saya sering menunda pekerjaan hingga mendekati tenggat.', 'C', true],
            ['Saya menjaga kerapian dokumen dan proses kerja.', 'C', false],
            ['Saya mudah terdistraksi saat mengerjakan tugas penting.', 'C', true],

            ['Saya merasa berenergi ketika banyak berinteraksi dengan orang lain.', 'E', false],
            ['Saya nyaman memulai percakapan dengan orang baru.', 'E', false],
            ['Saya lebih memilih aktivitas sosial dibanding bekerja sendiri terlalu lama.', 'E', false],
            ['Saya cenderung diam dalam forum diskusi besar.', 'E', true],
            ['Saya menghindari tampil di depan kelompok bila memungkinkan.', 'E', true],

            ['Saya berusaha memahami sudut pandang orang lain sebelum menilai.', 'A', false],
            ['Saya mudah bekerja sama dalam tim lintas peran.', 'A', false],
            ['Saya cenderung blak-blakan tanpa mempertimbangkan perasaan orang lain.', 'A', true],
            ['Saya bersedia membantu rekan kerja ketika diperlukan.', 'A', false],
            ['Saya sering terlibat konflik karena sulit berkompromi.', 'A', true],

            ['Saya tetap tenang saat menghadapi tekanan kerja.', 'N', true],
            ['Perubahan kecil membuat saya mudah cemas.', 'N', false],
            ['Saya sering memikirkan hal buruk sebelum terjadi.', 'N', false],
            ['Saya mampu pulih cepat setelah mengalami kegagalan.', 'N', true],
            ['Saya mudah tersinggung dalam situasi kerja yang menegangkan.', 'N', false],
        ];

        DB::transaction(function () use ($questions) {
            OceanQuestion::query()->delete();

            foreach ($questions as $index => $item) {
                OceanQuestion::create([
                    'question_number' => $index + 1,
                    'statement' => $item[0],
                    'trait' => $item[1],
                    'reverse_scored' => $item[2],
                ]);
            }
        });
    }
}
