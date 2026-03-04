<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\OceanAnswer;
use App\Models\OceanQuestion;
use App\Models\OceanTest;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OceanTestController extends Controller
{
    private const TIME_LIMIT_MINUTES = 10;

    public function start(string $code)
    {
        if (!Schema::hasTable('test_sessions')) {
            abort(500, 'Tabel test_sessions belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $session = TestSession::query()
            ->whereRaw('UPPER(code) = ?', [Str::upper($code)])
            ->where('test_type', 'OCEAN')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        return view('ocean.start', compact('session'));
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
            ->where('test_type', 'OCEAN')
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

        if (Schema::hasColumn('ocean_tests', 'client_id')) {
            $payload['client_id'] = $clientId;
        }
        if (Schema::hasColumn('ocean_tests', 'test_session_id')) {
            $payload['test_session_id'] = $session->id;
        }

        $test = OceanTest::create($payload);

        return redirect("/ocean/test/{$test->id}/question/1");
    }

    public function question(OceanTest $test, int $number)
    {
        $totalQuestions = OceanQuestion::count();
        if ($number < 1 || $number > $totalQuestions) {
            abort(404);
        }

        if ($this->isTimeExpired($test)) {
            return redirect("/ocean/test/{$test->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang tersimpan tetap diproses.');
        }

        $question = OceanQuestion::where('question_number', $number)->firstOrFail();
        $existingAnswer = OceanAnswer::where('ocean_test_id', $test->id)
            ->where('ocean_question_id', $question->id)
            ->first();
        $remainingSeconds = $this->remainingSeconds($test);

        return view('ocean.question', compact(
            'test',
            'question',
            'number',
            'existingAnswer',
            'remainingSeconds',
            'totalQuestions'
        ));
    }

    public function answer(Request $request, OceanTest $test)
    {
        if ($this->isTimeExpired($test)) {
            return redirect("/ocean/test/{$test->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang tersimpan tetap diproses.');
        }

        $totalQuestions = OceanQuestion::count();

        $validated = $request->validate([
            'ocean_question_id' => ['required', 'exists:ocean_questions,id'],
            'question_number' => ['required', 'integer', 'min:1', 'max:' . max($totalQuestions, 1)],
            'score' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        OceanAnswer::updateOrCreate(
            [
                'ocean_test_id' => $test->id,
                'ocean_question_id' => $validated['ocean_question_id'],
            ],
            [
                'score' => $validated['score'],
            ]
        );

        $next = $validated['question_number'] + 1;
        if ($next > $totalQuestions) {
            $test->update(['submitted_at' => now()]);
        }

        return $next <= $totalQuestions
            ? redirect("/ocean/test/{$test->id}/question/{$next}")
            : redirect("/ocean/test/{$test->id}/result");
    }

    private function remainingSeconds(OceanTest $test): int
    {
        if (!$test->started_at) {
            return self::TIME_LIMIT_MINUTES * 60;
        }

        $elapsed = max(0, now()->timestamp - $test->started_at->timestamp);

        return max(0, self::TIME_LIMIT_MINUTES * 60 - $elapsed);
    }

    private function isTimeExpired(OceanTest $test): bool
    {
        return $this->remainingSeconds($test) <= 0;
    }
}
