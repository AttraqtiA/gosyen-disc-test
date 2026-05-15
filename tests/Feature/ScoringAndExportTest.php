<?php

namespace Tests\Feature;

use App\Models\DiscAnswer;
use App\Models\DiscQuestion;
use App\Models\DiscResult;
use App\Models\DiscTest;
use App\Models\MbtiAnswer;
use App\Models\MbtiQuestion;
use App\Models\MbtiTest;
use App\Models\OceanAnswer;
use App\Models\OceanQuestion;
use App\Models\OceanTest;
use App\Models\TestSession;
use App\Models\User;
use Database\Seeders\DiscQuestionSeeder;
use Database\Seeders\MbtiQuestionSeeder;
use Database\Seeders\OceanQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_csv_export_keeps_columns_aligned_for_excel_and_comma_values(): void
    {
        $session = TestSession::create([
            'name' => '4 April, Batch A',
            'code' => 'DISC-CSV',
            'test_type' => 'DISC',
            'is_active' => true,
        ]);

        $test = DiscTest::create([
            ...$this->baseParticipant(),
            'test_session_id' => $session->id,
            'nama' => 'Jessica, QA',
            'jabatan_saat_ini' => 'Quality Assurance Analyst, Senior',
            'tanggal_tes' => now(),
            'started_at' => now()->subMinutes(8),
            'submitted_at' => now(),
        ]);

        DiscResult::create([
            'disc_test_id' => $test->id,
            'd_score' => 0,
            'i_score' => -2,
            's_score' => 0,
            'c_score' => 1,
            'dominant_type' => 'C',
        ]);

        $admin = User::create([
            'name' => 'Admin CSV',
            'email' => 'admin.csv@example.com',
            'password' => 'password',
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($admin)->get("/admin/exports/sessions/{$session->id}.csv");

        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBFsep=,\r\n", $csv);

        [$header, $row] = $this->csvHeaderAndFirstRow($csv);

        $this->assertCount(count($header), $row);
        $this->assertSame('4 April, Batch A', $row[array_search('session_name', $header, true)]);
        $this->assertSame('Jessica, QA', $row[array_search('nama', $header, true)]);
        $this->assertSame(
            'D=0;I=-2;S=0;C=1;Dominan=C',
            $row[array_search('result_summary', $header, true)]
        );
    }

    public function test_disc_result_uses_most_minus_least_scoring(): void
    {
        $this->seed(DiscQuestionSeeder::class);
        $session = $this->createTestSession('DISC');
        $test = DiscTest::create([
            ...$this->baseParticipant(),
            'test_session_id' => $session->id,
            'tanggal_tes' => now(),
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        $expected = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
        DiscQuestion::with('statements')->orderBy('question_number')->get()->each(function ($question) use ($test, &$expected) {
            $p = $question->statements[0];
            $k = $question->statements[1];

            DiscAnswer::create([
                'disc_test_id' => $test->id,
                'disc_question_id' => $question->id,
                'p_statement_id' => $p->id,
                'k_statement_id' => $k->id,
            ]);

            if (isset($expected[$p->disc_type])) {
                $expected[$p->disc_type]++;
            }
            if (isset($expected[$k->disc_type])) {
                $expected[$k->disc_type]--;
            }
        });

        $this->get("/test/{$test->id}/result")->assertOk();

        $dominant = collect($expected)->sortDesc()->keys()->first();
        $this->assertDatabaseHas('disc_results', [
            'disc_test_id' => $test->id,
            'd_score' => $expected['D'],
            'i_score' => $expected['I'],
            's_score' => $expected['S'],
            'c_score' => $expected['C'],
            'dominant_type' => $dominant,
        ]);
    }

    public function test_ocean_result_applies_reverse_scoring_per_trait(): void
    {
        $this->seed(OceanQuestionSeeder::class);
        $session = $this->createTestSession('OCEAN');
        $test = OceanTest::create([
            ...$this->baseParticipant(),
            'test_session_id' => $session->id,
            'tanggal_tes' => now(),
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        OceanQuestion::all()->each(fn ($question) => OceanAnswer::create([
            'ocean_test_id' => $test->id,
            'ocean_question_id' => $question->id,
            'score' => 5,
        ]));

        $this->get("/ocean/test/{$test->id}/result")->assertOk();

        $this->assertDatabaseHas('ocean_results', [
            'ocean_test_id' => $test->id,
            'o_score' => 17,
            'c_score' => 17,
            'e_score' => 17,
            'a_score' => 17,
            'n_score' => 17,
            'dominant_trait' => 'O',
        ]);
    }

    public function test_mbti_result_composes_type_from_pair_scores(): void
    {
        $this->seed(MbtiQuestionSeeder::class);
        $session = $this->createTestSession('MBTI');
        $test = MbtiTest::create([
            ...$this->baseParticipant(),
            'test_session_id' => $session->id,
            'tanggal_tes' => now(),
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        MbtiQuestion::all()->each(fn ($question) => MbtiAnswer::create([
            'mbti_test_id' => $test->id,
            'mbti_question_id' => $question->id,
            'selected_trait' => $question->trait_a,
        ]));

        $this->get("/mbti/test/{$test->id}/result")->assertOk();

        $this->assertDatabaseHas('mbti_results', [
            'mbti_test_id' => $test->id,
            'e_score' => 8,
            'i_score' => 0,
            's_score' => 8,
            'n_score' => 0,
            't_score' => 8,
            'f_score' => 0,
            'j_score' => 8,
            'p_score' => 0,
            'type_code' => 'ESTJ',
        ]);
    }

    private function csvHeaderAndFirstRow(string $csv): array
    {
        $lines = array_values(array_filter(
            preg_split('/\r\n|\n|\r/', trim($csv)),
            fn ($line) => trim($line, "\xEF\xBB\xBF") !== 'sep=,'
        ));

        return [
            str_getcsv($lines[0]),
            str_getcsv($lines[1]),
        ];
    }

    private function createTestSession(string $type): TestSession
    {
        return TestSession::create([
            'name' => "{$type} Session",
            'code' => "{$type}-SESSION",
            'test_type' => $type,
            'is_active' => true,
        ]);
    }

    private function baseParticipant(): array
    {
        return [
            'nama' => 'Peserta QA',
            'email' => 'peserta.qa@example.com',
            'nomor_hp' => '08123456789',
            'institusi_perusahaan' => 'PT QA',
            'departemen_divisi' => 'Quality',
            'jabatan_saat_ini' => 'Staff',
            'usia' => 27,
            'jenis_kelamin' => 'L',
            'pendidikan_terakhir' => 'S1',
            'lama_pengalaman_kerja' => 3,
            'lokasi_kota' => 'Jakarta',
            'tujuan_tes' => 'Rekrutmen',
        ];
    }
}
