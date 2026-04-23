<?php

namespace Tests\Feature;

use App\Models\DiscQuestion;
use App\Models\DiscTest;
use App\Models\TestSession;
use App\Models\User;
use Database\Seeders\DiscQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_disc_question_page_is_accessible_for_fresh_test_and_redirects_when_expired(): void
    {
        $this->seed(DiscQuestionSeeder::class);
        $session = $this->createDiscSession();

        $freshTest = DiscTest::create([
            'test_session_id' => $session->id,
            'nama' => 'Tester Fresh',
            'institusi_perusahaan' => 'PT Demo',
            'departemen_divisi' => 'Ops',
            'usia' => 25,
            'jenis_kelamin' => 'L',
            'tanggal_tes' => now(),
            'started_at' => now(),
        ]);

        $expiredTest = DiscTest::create([
            'test_session_id' => $session->id,
            'nama' => 'Tester Expired',
            'institusi_perusahaan' => 'PT Demo',
            'departemen_divisi' => 'Ops',
            'usia' => 25,
            'jenis_kelamin' => 'L',
            'tanggal_tes' => now(),
            'started_at' => now()->subMinutes(16),
        ]);

        $this->get("/test/{$freshTest->id}/question/1")->assertOk();
        $this->get("/test/{$expiredTest->id}/question/1")->assertRedirect("/test/{$expiredTest->id}/result");
    }

    public function test_disc_answers_are_saved_and_manual_export_contains_answered_count(): void
    {
        $this->seed(DiscQuestionSeeder::class);
        $session = $this->createDiscSession();

        $response = $this->post('/start', [
            'access_code' => $session->code,
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
        ]);

        $testId = (int) DiscTest::query()->value('id');
        $response->assertRedirect("/test/{$testId}/question/1");

        $questions = DiscQuestion::with('statements')->orderBy('question_number')->get();
        foreach ($questions as $question) {
            $statements = $question->statements->values();
            $action = $question->question_number === 24 ? 'finish' : 'next';

            $this->post("/test/{$testId}/answer", [
                'disc_question_id' => $question->id,
                'question_number' => $question->question_number,
                'p' => $statements[0]->id,
                'k' => $statements[1]->id,
                'action' => $action,
            ])->assertRedirect();
        }

        $test = DiscTest::withCount('answers')->findOrFail($testId);
        $this->assertNotNull($test->submitted_at);
        $this->assertSame(24, $test->answers_count);

        $admin = User::create([
            'name' => 'Admin QA',
            'email' => 'admin.qa@example.com',
            'password' => 'password',
            'role' => 'superadmin',
        ]);

        $csvResponse = $this->actingAs($admin)->get("/admin/exports/disc/manual.csv?session_id={$session->id}");
        $csvResponse->assertOk();

        $csv = trim($csvResponse->streamedContent());
        $lines = preg_split('/\r\n|\n|\r/', $csv);
        $this->assertGreaterThanOrEqual(2, count($lines));

        $header = str_getcsv($lines[0]);
        $row = str_getcsv($lines[1]);
        $idxAnswered = array_search('jumlah_terjawab', $header, true);

        $this->assertNotFalse($idxAnswered);
        $this->assertSame('24', $row[$idxAnswered]);
    }

    public function test_disc_cannot_finish_if_not_all_questions_are_answered(): void
    {
        $this->seed(DiscQuestionSeeder::class);
        $session = $this->createDiscSession();

        $this->post('/start', [
            'access_code' => $session->code,
            'nama' => 'Peserta Incomplete',
            'institusi_perusahaan' => 'PT QA',
            'departemen_divisi' => 'Quality',
            'usia' => 27,
            'jenis_kelamin' => 'L',
        ]);

        $testId = (int) DiscTest::query()->value('id');
        $firstQuestion = DiscQuestion::with('statements')->where('question_number', 1)->firstOrFail();

        $response = $this->from("/test/{$testId}/question/1")
            ->post("/test/{$testId}/answer", [
                'disc_question_id' => $firstQuestion->id,
                'question_number' => 1,
                'p' => $firstQuestion->statements[0]->id,
                'k' => $firstQuestion->statements[1]->id,
                'action' => 'finish',
            ]);

        $response->assertRedirect("/test/{$testId}/question/1");
        $response->assertSessionHasErrors('p');
        $this->assertNull(DiscTest::findOrFail($testId)->submitted_at);
    }

    public function test_disc_goto_action_saves_current_answer_before_redirecting(): void
    {
        $this->seed(DiscQuestionSeeder::class);
        $session = $this->createDiscSession();

        $this->post('/start', [
            'access_code' => $session->code,
            'nama' => 'Peserta Goto',
            'institusi_perusahaan' => 'PT QA',
            'departemen_divisi' => 'Quality',
            'usia' => 27,
            'jenis_kelamin' => 'L',
        ]);

        $testId = (int) DiscTest::query()->value('id');
        $firstQuestion = DiscQuestion::with('statements')->where('question_number', 1)->firstOrFail();

        $response = $this->post("/test/{$testId}/answer", [
            'disc_question_id' => $firstQuestion->id,
            'question_number' => 1,
            'p' => $firstQuestion->statements[0]->id,
            'k' => $firstQuestion->statements[1]->id,
            'action' => 'goto',
            'target_number' => 7,
        ]);

        $response->assertRedirect("/test/{$testId}/question/7");
        $this->assertDatabaseHas('disc_answers', [
            'disc_test_id' => $testId,
            'disc_question_id' => $firstQuestion->id,
            'p_statement_id' => $firstQuestion->statements[0]->id,
            'k_statement_id' => $firstQuestion->statements[1]->id,
        ]);
    }

    private function createDiscSession(): TestSession
    {
        return TestSession::create([
            'name' => 'Session DISC QA',
            'code' => 'DISC-QA-01',
            'test_type' => 'DISC',
            'is_active' => true,
        ]);
    }
}
