<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscTest;
use App\Models\DiscQuestion;
use App\Models\DiscAnswer;
use App\Models\Client;
use App\Models\TestSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DiscTestController extends Controller
{
    private const TOTAL_QUESTIONS = 24;
    private const TIME_LIMIT_MINUTES = 15;

    public function codeEntry()
    {
        return view('disc.code-entry');
    }

    public function accessByCode(Request $request)
    {
        if (!Schema::hasTable('test_sessions')) {
            abort(500, 'Tabel test_sessions belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $session = TestSession::query()
            ->whereRaw('UPPER(code) = ?', [Str::upper($validated['code'])])
            ->where('test_type', 'DISC')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$session) {
            return back()->withErrors([
                'code' => 'Kode tidak valid atau sudah tidak aktif.',
            ])->withInput();
        }

        return redirect('/start/' . $session->code);
    }

    public function start(string $code)
    {
        if (!Schema::hasTable('test_sessions')) {
            abort(500, 'Tabel test_sessions belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $session = TestSession::query()
            ->whereRaw('UPPER(code) = ?', [Str::upper($code)])
            ->where('test_type', 'DISC')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        return view('disc.start', compact('session'));
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
            ->where('test_type', 'DISC')
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

        if (Schema::hasColumn('disc_tests', 'client_id')) {
            $payload['client_id'] = $clientId;
        }
        if (Schema::hasColumn('disc_tests', 'test_session_id')) {
            $payload['test_session_id'] = $session->id;
        }

        $test = DiscTest::create($payload);

        return redirect("/test/{$test->id}/question/1");
    }

    public function question(DiscTest $test, $number)
    {
        if ($number < 1 || $number > self::TOTAL_QUESTIONS) {
            abort(404);
        }

        if ($this->isTimeExpired($test)) {
            return redirect("/test/{$test->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang tersimpan tetap diproses.');
        }

        $question = DiscQuestion::where('question_number', $number)
            ->with('statements')
            ->firstOrFail();

        $existingAnswer = DiscAnswer::where('disc_test_id', $test->id)
            ->where('disc_question_id', $question->id)
            ->first();

        $remainingSeconds = $this->remainingSeconds($test);

        return view('disc.question', compact('test', 'question', 'number', 'existingAnswer', 'remainingSeconds'));
    }

    public function answer(Request $request, DiscTest $test)
    {
        if ($this->isTimeExpired($test)) {
            return redirect("/test/{$test->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang tersimpan tetap diproses.');
        }

        $validated = $request->validate([
            'disc_question_id' => ['required', 'exists:disc_questions,id'],
            'question_number' => ['required', 'integer', 'min:1', 'max:' . self::TOTAL_QUESTIONS],
            'p' => ['required', 'different:k', 'exists:disc_statements,id'],
            'k' => ['required', 'different:p', 'exists:disc_statements,id'],
        ]);

        $statementCount = DiscQuestion::whereKey($validated['disc_question_id'])
            ->whereHas('statements', function ($query) use ($validated) {
                $query->whereIn('id', [$validated['p'], $validated['k']]);
            }, '=', 2)
            ->count();

        if ($statementCount === 0) {
            return back()->withErrors([
                'p' => 'Pilihan P dan K harus berasal dari nomor soal yang sama.',
            ])->withInput();
        }

        DiscAnswer::updateOrCreate(
            [
                'disc_test_id' => $test->id,
                'disc_question_id' => $validated['disc_question_id'],
            ],
            [
                'p_statement_id' => $validated['p'],
                'k_statement_id' => $validated['k'],
            ]
        );

        $next = $validated['question_number'] + 1;

        if ($next > self::TOTAL_QUESTIONS) {
            $test->update(['submitted_at' => now()]);
        }

        return $next <= self::TOTAL_QUESTIONS
            ? redirect("/test/{$test->id}/question/{$next}")
            : redirect("/test/{$test->id}/result");
    }

    private function remainingSeconds(DiscTest $test): int
    {
        if (!$test->started_at) {
            return self::TIME_LIMIT_MINUTES * 60;
        }

        return max(0, self::TIME_LIMIT_MINUTES * 60 - now()->diffInSeconds($test->started_at));
    }

    private function isTimeExpired(DiscTest $test): bool
    {
        return $this->remainingSeconds($test) <= 0;
    }
}
