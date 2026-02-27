<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscQuestion;
use App\Models\DiscStatement;
use Illuminate\Support\Facades\DB;

class DiscQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            DiscStatement::query()->delete();
            DiscQuestion::query()->delete();

            $questions = [
                1 => [
                    ['text' => 'Gampang gaul, Mudah setuju', 'disc_type' => 'S'],
                    ['text' => 'Percaya, Mudah percaya pada orang', 'disc_type' => 'I'],
                    ['text' => 'Petualang, Mengambil resiko', 'disc_type' => 'X'],
                    ['text' => 'Toleran, Menghormati', 'disc_type' => 'C'],
                ],
                2 => [
                    ['text' => 'Lembut suara, Pendiam', 'disc_type' => 'C'],
                    ['text' => 'Optimistik, Visioner', 'disc_type' => 'D'],
                    ['text' => 'Pusat Perhatian, Suka gaul', 'disc_type' => 'X'],
                    ['text' => 'Pendamai, Membawa Harmoni', 'disc_type' => 'S'],
                ],
                3 => [
                    ['text' => 'Menyemangati orang', 'disc_type' => 'I'],
                    ['text' => 'Berusaha sempurna', 'disc_type' => 'X'],
                    ['text' => 'Bagian dari kelompok', 'disc_type' => 'X'],
                    ['text' => 'Ingin membuat tujuan', 'disc_type' => 'D'],
                ],
                4 => [
                    ['text' => 'Menjadi frustrasi', 'disc_type' => 'C'],
                    ['text' => 'Menyimpan perasaan saya', 'disc_type' => 'S'],
                    ['text' => 'Menceritakan sisi saya', 'disc_type' => 'X'],
                    ['text' => 'Siap beroposisi', 'disc_type' => 'D'],
                ],
                5 => [
                    ['text' => 'Hidup, Suka bicara', 'disc_type' => 'I'],
                    ['text' => 'Gerak cepat, Tekun', 'disc_type' => 'D'],
                    ['text' => 'Usaha menjaga keseimbangan', 'disc_type' => 'S'],
                    ['text' => 'Usaha mengikuti aturan', 'disc_type' => 'X'],
                ],
                6 => [
                    ['text' => 'Kelola waktu secara efisien', 'disc_type' => 'C'],
                    ['text' => 'Sering terburu-buru, Merasa tertekan', 'disc_type' => 'D'],
                    ['text' => 'Masalah sosial itu penting', 'disc_type' => 'I'],
                    ['text' => 'Suka selesaikan apa yang saya mulai', 'disc_type' => 'S'],
                ],
                7 => [
                    ['text' => 'Tolak perubahan mendadak', 'disc_type' => 'S'],
                    ['text' => 'Cenderung janji berlebihan', 'disc_type' => 'I'],
                    ['text' => 'Tarik diri di tengah tekanan', 'disc_type' => 'X'],
                    ['text' => 'Tidak takut bertempur', 'disc_type' => 'X'],
                ],
                8 => [
                    ['text' => 'Penyemangat yang baik', 'disc_type' => 'I'],
                    ['text' => 'Pendengar yang baik', 'disc_type' => 'S'],
                    ['text' => 'Penganalisa yang baik', 'disc_type' => 'C'],
                    ['text' => 'Delegator yang baik', 'disc_type' => 'D'],
                ],
                9 => [
                    ['text' => 'Hasil adalah penting', 'disc_type' => 'D'],
                    ['text' => 'Lakukan dengan benar, Akurasi penting', 'disc_type' => 'C'],
                    ['text' => 'Dibuat menyenangkan', 'disc_type' => 'X'],
                    ['text' => 'Mari kerjakan bersama', 'disc_type' => 'X'],
                ],
                10 => [
                    ['text' => 'Akan berjalan terus tanpa kontrol diri', 'disc_type' => 'X'],
                    ['text' => 'Akan membeli sesuai dorongan hati', 'disc_type' => 'D'],
                    ['text' => 'Akan menunggu, Tanpa tekanan', 'disc_type' => 'S'],
                    ['text' => 'Akan mengusahakan  yang kuinginkan', 'disc_type' => 'I'],
                ],
                11 => [
                    ['text' => 'Ramah, Mudah bergabung', 'disc_type' => 'S'],
                    ['text' => 'Unik, Bosan rutinitas', 'disc_type' => 'X'],
                    ['text' => 'Aktif mengubah sesuatu', 'disc_type' => 'D'],
                    ['text' => 'Ingin hal-hal yang pasti', 'disc_type' => 'C'],
                ],
                12 => [
                    ['text' => 'Non-konfrontasi, Menyerah', 'disc_type' => 'X'],
                    ['text' => 'Dipenuhi hal detail', 'disc_type' => 'C'],
                    ['text' => 'Perubahan pada menit terakhir', 'disc_type' => 'I'],
                    ['text' => 'Menuntut, Kasar', 'disc_type' => 'D'],
                ],
                13 => [
                    ['text' => 'Ingin kemajuan', 'disc_type' => 'D'],
                    ['text' => 'Puas dengan segalanya', 'disc_type' => 'S'],
                    ['text' => 'Terbuka memperlihatkan perasaan', 'disc_type' => 'I'],
                    ['text' => 'Rendah hati, Sederhana', 'disc_type' => 'X'],
                ],
                14 => [
                    ['text' => 'Tenang, Pendiam', 'disc_type' => 'C'],
                    ['text' => 'Bahagia, Tanpa beban', 'disc_type' => 'I'],
                    ['text' => 'Menyenangkan, Baik hati', 'disc_type' => 'S'],
                    ['text' => 'Tak gentar, Berani', 'disc_type' => 'D'],
                ],
                15 => [
                    ['text' => 'Menggunakan waktu berkualitas dgn teman', 'disc_type' => 'S'],
                    ['text' => 'Rencanakan masa depan, Bersiap', 'disc_type' => 'C'],
                    ['text' => 'Bepergian demi petualangan baru', 'disc_type' => 'I'],
                    ['text' => 'Menerima ganjaran atas tujuan yg dicapai', 'disc_type' => 'D'],
                ],
                16 => [
                    ['text' => 'Aturan perlu dipertanyakan', 'disc_type' => 'X'],
                    ['text' => 'Aturan membuat adil', 'disc_type' => 'C'],
                    ['text' => 'Aturan membuat bosan', 'disc_type' => 'I'],
                    ['text' => 'Aturan membuat aman', 'disc_type' => 'S'],
                ],
                17 => [
                    ['text' => 'Pendidikan, Kebudayaan', 'disc_type' => 'X'],
                    ['text' => 'Prestasi, Ganjaran', 'disc_type' => 'D'],
                    ['text' => 'Keselamatan, keamanan', 'disc_type' => 'S'],
                    ['text' => 'Sosial, Perkumpulan kelompok', 'disc_type' => 'I'],
                ],
                18 => [
                    ['text' => 'Memimpin, Pendekatan langsung', 'disc_type' => 'D'],
                    ['text' => 'Suka bergaul, Antusias', 'disc_type' => 'X'],
                    ['text' => 'Dapat diramal, Konsisten', 'disc_type' => 'X'],
                    ['text' => 'Waspada, Hati-hati', 'disc_type' => 'C'],
                ],
                19 => [
                    ['text' => 'Tidak mudah dikalahkan', 'disc_type' => 'D'],
                    ['text' => 'Kerjakan sesuai perintah, Ikut pimpinan', 'disc_type' => 'S'],
                    ['text' => 'Mudah terangsang, Riang', 'disc_type' => 'I'],
                    ['text' => 'Ingin segalanya teratur, Rapi', 'disc_type' => 'X'],
                ],
                20 => [
                    ['text' => 'Saya akan pimpin mereka', 'disc_type' => 'D'],
                    ['text' => 'Saya akan melaksanakan', 'disc_type' => 'S'],
                    ['text' => 'Saya akan meyakinkan mereka', 'disc_type' => 'I'],
                    ['text' => 'Saya dapatkan fakta', 'disc_type' => 'C'],
                ],
                21 => [
                    ['text' => 'Memikirkan orang dahulu', 'disc_type' => 'S'],
                    ['text' => 'Kompetitif, Suka tantangan', 'disc_type' => 'D'],
                    ['text' => 'Optimis, Positif', 'disc_type' => 'I'],
                    ['text' => 'Pemikir logis, Sistematik', 'disc_type' => 'X'],
                ],
                22 => [
                    ['text' => 'Menyenangkan orang, Mudah setuju', 'disc_type' => 'S'],
                    ['text' => 'Tertawa lepas, Hidup', 'disc_type' => 'X'],
                    ['text' => 'Berani, Tak gentar', 'disc_type' => 'D'],
                    ['text' => 'Tenang, Pendiam', 'disc_type' => 'C'],
                ],
                23 => [
                    ['text' => 'Ingin otoritas lebih', 'disc_type' => 'X'],
                    ['text' => 'Ingin kesempatan baru', 'disc_type' => 'I'],
                    ['text' => 'Menghindari konflik', 'disc_type' => 'S'],
                    ['text' => 'Ingin petunjuk yang jelas', 'disc_type' => 'X'],
                ],
                24 => [
                    ['text' => 'Dapat diandalkan, Dapata dipercaya', 'disc_type' => 'X'],
                    ['text' => 'Kreatif, Unik', 'disc_type' => 'I'],
                    ['text' => 'Garis dasar, Orientasi hasil', 'disc_type' => 'D'],
                    ['text' => 'Jalankan standar yang tinggi, Akurat', 'disc_type' => 'C'],
                ],
            ];

            foreach ($questions as $questionNumber => $statements) {
                $question = DiscQuestion::create([
                    'question_number' => $questionNumber,
                ]);

                foreach ($statements as $statement) {
                    DiscStatement::create([
                        'disc_question_id' => $question->id,
                        'text' => $statement['text'],
                        'disc_type' => $statement['disc_type'],
                    ]);
                }
            }
        });
    }
}
