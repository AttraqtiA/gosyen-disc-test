<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MbtiAnswer;
use App\Models\MbtiQuestion;
use App\Models\MbtiTest;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MbtiTestController extends Controller
{
    private const TIME_LIMIT_MINUTES = 20;

    public function start(string $code)
    {
        if (!Schema::hasTable('test_sessions')) {
            abort(500, 'Tabel test_sessions belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $session = TestSession::query()
            ->whereRaw('UPPER(code) = ?', [Str::upper($code)])
            ->where('test_type', 'MBTI')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        return view('mbti.start', compact('session'));
    }

    public function storeMeta(Request $request)
    {
        if (!Schema::hasTable('test_sessions')) {
            abort(500, 'Tabel test_sessions belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $validated = $request->validate([
            'access_code' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
            'institusi_perusahaan' => ['required', 'string', 'max:255'],
            'departemen_divisi' => ['required', 'string', 'max:255'],
            'jabatan_saat_ini' => ['nullable', 'string', 'max:255'],
            'usia' => ['required', 'integer', 'min:15', 'max:80'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:255'],
            'lama_pengalaman_kerja' => ['nullable', 'integer', 'min:0', 'max:60'],
            'lokasi_kota' => ['nullable', 'string', 'max:255'],
            'tujuan_tes' => ['nullable', 'string', 'max:255'],
        ]);

        $session = TestSession::query()
            ->whereRaw('UPPER(code) = ?', [Str::upper($validated['access_code'])])
            ->where('test_type', 'MBTI')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$session) {
            return redirect('/')->withErrors([
                'code' => 'Kode sesi tidak valid atau sudah tidak aktif.',
            ]);
        }

        $clientId = null;

        if (Schema::hasTable('clients') && $session->client_id) {
            $clientId = $session->client_id;
        } elseif (Schema::hasTable('clients')) {
            $client = Client::firstOrCreate(
                ['name' => $validated['institusi_perusahaan']],
                ['code' => Str::slug($validated['institusi_perusahaan']) . '-' . Str::lower(Str::random(5))]
            );

            $clientId = $client->id;
        }

        $payload = [
            ...$validated,
            'tanggal_tes' => now(),
            'started_at' => now(),
        ];

        if (Schema::hasColumn('mbti_tests', 'client_id')) {
            $payload['client_id'] = $clientId;
        }
        if (Schema::hasColumn('mbti_tests', 'test_session_id')) {
            $payload['test_session_id'] = $session->id;
        }

        $test = MbtiTest::create($payload);

        return redirect("/mbti/test/{$test->id}/question/1");
    }

    public function question(MbtiTest $test, int $number)
    {
        $totalQuestions = MbtiQuestion::count();
        if ($number < 1 || $number > $totalQuestions) {
            abort(404);
        }

        if ($this->isTimeExpired($test)) {
            return redirect("/mbti/test/{$test->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang tersimpan tetap diproses.');
        }

        $question = MbtiQuestion::where('question_number', $number)->firstOrFail();
        $existingAnswer = MbtiAnswer::where('mbti_test_id', $test->id)
            ->where('mbti_question_id', $question->id)
            ->first();
        $remainingSeconds = $this->remainingSeconds($test);

        return view('mbti.question', compact(
            'test',
            'question',
            'number',
            'existingAnswer',
            'remainingSeconds',
            'totalQuestions'
        ));
    }

    public function answer(Request $request, MbtiTest $test)
    {
        if ($this->isTimeExpired($test)) {
            return redirect("/mbti/test/{$test->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang tersimpan tetap diproses.');
        }

        $totalQuestions = MbtiQuestion::count();

        $validated = $request->validate([
            'mbti_question_id' => ['required', 'exists:mbti_questions,id'],
            'question_number' => ['required', 'integer', 'min:1', 'max:' . max($totalQuestions, 1)],
            'selected_trait' => ['required', 'in:E,I,S,N,T,F,J,P'],
        ]);

        $question = MbtiQuestion::findOrFail($validated['mbti_question_id']);
        if (!in_array($validated['selected_trait'], [$question->trait_a, $question->trait_b], true)) {
            return back()->withErrors([
                'selected_trait' => 'Pilihan jawaban tidak valid untuk nomor soal ini.',
            ])->withInput();
        }

        MbtiAnswer::updateOrCreate(
            [
                'mbti_test_id' => $test->id,
                'mbti_question_id' => $question->id,
            ],
            [
                'selected_trait' => $validated['selected_trait'],
            ]
        );

        $next = $validated['question_number'] + 1;
        if ($next > $totalQuestions) {
            $test->update(['submitted_at' => now()]);
        }

        return $next <= $totalQuestions
            ? redirect("/mbti/test/{$test->id}/question/{$next}")
            : redirect("/mbti/test/{$test->id}/result");
    }

    private function remainingSeconds(MbtiTest $test): int
    {
        if (!$test->started_at) {
            return self::TIME_LIMIT_MINUTES * 60;
        }

        return max(0, self::TIME_LIMIT_MINUTES * 60 - now()->diffInSeconds($test->started_at));
    }

    private function isTimeExpired(MbtiTest $test): bool
    {
        return $this->remainingSeconds($test) <= 0;
    }
}
