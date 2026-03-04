<?php

namespace Database\Seeders;

use App\Models\MbtiQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MbtiQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['Saat bertemu orang baru, saya cenderung langsung membuka percakapan.', 'Saat bertemu orang baru, saya cenderung menunggu diajak bicara lebih dulu.', 'E', 'I'],
            ['Saya merasa berenergi setelah banyak interaksi sosial.', 'Saya merasa berenergi setelah punya waktu sendiri.', 'E', 'I'],
            ['Saya lebih mudah berpikir sambil berdiskusi.', 'Saya lebih mudah berpikir saat merenung sendiri.', 'E', 'I'],
            ['Dalam rapat, saya nyaman menyampaikan ide secara spontan.', 'Dalam rapat, saya lebih nyaman menyampaikan ide setelah dipikirkan matang.', 'E', 'I'],
            ['Saat bekerja tim, saya suka terlibat aktif di banyak percakapan.', 'Saat bekerja tim, saya lebih memilih kontribusi yang tenang dan fokus.', 'E', 'I'],
            ['Saya cenderung cepat merespons chat atau panggilan.', 'Saya cenderung merespons setelah menimbang jawaban dengan tenang.', 'E', 'I'],
            ['Saya lebih nyaman mengenal banyak orang sekaligus.', 'Saya lebih nyaman membangun kedekatan dengan beberapa orang saja.', 'E', 'I'],
            ['Saya suka suasana kerja yang ramai dan dinamis.', 'Saya suka suasana kerja yang tenang dan minim distraksi.', 'E', 'I'],

            ['Saya lebih percaya data yang konkret dan terukur.', 'Saya lebih tertarik pada pola, konsep, dan kemungkinan baru.', 'S', 'N'],
            ['Saya cenderung fokus pada fakta yang terjadi saat ini.', 'Saya cenderung fokus pada potensi jangka panjang.', 'S', 'N'],
            ['Saya nyaman mengikuti prosedur yang sudah terbukti.', 'Saya suka bereksperimen dengan pendekatan baru.', 'S', 'N'],
            ['Saat memecahkan masalah, saya mulai dari detail yang terlihat.', 'Saat memecahkan masalah, saya mulai dari gambaran besar.', 'S', 'N'],
            ['Saya menyukai instruksi yang jelas dan langkah demi langkah.', 'Saya menyukai kebebasan untuk menafsirkan arah kerja.', 'S', 'N'],
            ['Saya lebih mudah belajar dari contoh praktik langsung.', 'Saya lebih mudah belajar dari konsep dan kerangka teori.', 'S', 'N'],
            ['Saya cenderung realistis terhadap apa yang mungkin dilakukan sekarang.', 'Saya cenderung visioner terhadap apa yang mungkin terjadi nanti.', 'S', 'N'],
            ['Saya menilai ide dari kelayakan praktisnya.', 'Saya menilai ide dari nilai kebaruannya.', 'S', 'N'],

            ['Saya mengambil keputusan terutama dengan logika objektif.', 'Saya mengambil keputusan terutama dengan mempertimbangkan dampak ke orang.', 'T', 'F'],
            ['Saya nyaman memberi umpan balik secara tegas dan langsung.', 'Saya lebih memilih memberi umpan balik dengan empati dan kehati-hatian.', 'T', 'F'],
            ['Saat ada konflik, saya fokus pada solusi yang paling rasional.', 'Saat ada konflik, saya fokus pada hubungan agar tetap harmonis.', 'T', 'F'],
            ['Saya menilai kinerja berdasarkan standar dan hasil.', 'Saya menilai kinerja dengan mempertimbangkan usaha dan kondisi personal.', 'T', 'F'],
            ['Saya cenderung memisahkan urusan pribadi dari keputusan kerja.', 'Saya cenderung memasukkan faktor kemanusiaan dalam keputusan kerja.', 'T', 'F'],
            ['Saya lebih nyaman berdebat untuk mencari kebenaran.', 'Saya lebih nyaman berdialog untuk menjaga kesepahaman.', 'T', 'F'],
            ['Saya cenderung adil dengan aturan yang sama untuk semua.', 'Saya cenderung menyesuaikan pendekatan sesuai kebutuhan tiap orang.', 'T', 'F'],
            ['Saya biasanya menilai ide dari keakuratan logikanya.', 'Saya biasanya menilai ide dari keselarasan dengan nilai tim.', 'T', 'F'],

            ['Saya suka rencana kerja yang jelas sejak awal.', 'Saya lebih suka rencana fleksibel yang bisa berubah sesuai situasi.', 'J', 'P'],
            ['Saya nyaman menyelesaikan tugas jauh sebelum tenggat.', 'Saya sering bekerja paling efektif mendekati tenggat.', 'J', 'P'],
            ['Saya lebih tenang jika keputusan sudah ditetapkan.', 'Saya lebih tenang jika pilihan tetap terbuka lebih lama.', 'J', 'P'],
            ['Saya menyukai daftar prioritas yang terstruktur.', 'Saya menyukai cara kerja adaptif sesuai peluang yang muncul.', 'J', 'P'],
            ['Saya cenderung menutup pekerjaan satu per satu.', 'Saya cenderung menangani beberapa hal paralel dan dinamis.', 'J', 'P'],
            ['Saya tidak nyaman jika agenda berubah mendadak.', 'Saya cukup nyaman jika agenda berubah mendadak.', 'J', 'P'],
            ['Saya suka lingkungan kerja yang tertib dan terjadwal.', 'Saya suka lingkungan kerja yang spontan dan variatif.', 'J', 'P'],
            ['Saya biasanya membuat keputusan final lebih cepat.', 'Saya biasanya menunda keputusan untuk mengumpulkan opsi tambahan.', 'J', 'P'],
        ];

        DB::transaction(function () use ($questions) {
            MbtiQuestion::query()->delete();

            foreach ($questions as $index => $item) {
                MbtiQuestion::create([
                    'question_number' => $index + 1,
                    'text_a' => $item[0],
                    'text_b' => $item[1],
                    'trait_a' => $item[2],
                    'trait_b' => $item[3],
                ]);
            }
        });
    }
}
