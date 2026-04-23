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

        $testType = strtoupper($session->test_type);

        if ($testType === 'MBTI') {
            return redirect('/mbti/start/' . $session->code);
        }
        if ($testType === 'OCEAN') {
            return redirect('/ocean/start/' . $session->code);
        }

        if ($testType !== 'DISC') {
            return back()->withErrors([
                'code' => 'Tipe tes untuk kode ini belum tersedia.',
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

        $answeredNumbers = DiscAnswer::where('disc_test_id', $test->id)
            ->pluck('disc_question_id')
            ->all();

        $questionNumbersById = DiscQuestion::query()
            ->whereIn('id', $answeredNumbers)
            ->pluck('question_number')
            ->map(fn ($value) => (int) $value)
            ->all();

        $remainingSeconds = $this->remainingSeconds($test);

        return view('disc.question', compact('test', 'question', 'number', 'existingAnswer', 'remainingSeconds', 'questionNumbersById'));
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
            'p' => ['nullable', 'different:k', 'exists:disc_statements,id'],
            'k' => ['nullable', 'different:p', 'exists:disc_statements,id'],
            'action' => ['nullable', 'in:prev,next,goto,finish'],
            'target_number' => ['nullable', 'integer', 'min:1', 'max:' . self::TOTAL_QUESTIONS],
        ]);

        $action = $validated['action'] ?? 'next';
        $hasAnyAnswer = filled($validated['p'] ?? null) || filled($validated['k'] ?? null);
        $hasCompleteAnswer = filled($validated['p'] ?? null) && filled($validated['k'] ?? null);

        if ($hasAnyAnswer && !$hasCompleteAnswer) {
            return back()->withErrors([
                'p' => 'Lengkapi pilihan P dan K untuk menyimpan jawaban nomor ini.',
            ])->withInput();
        }

        if ($action === 'finish' && !$hasCompleteAnswer && !$this->hasSavedAnswer($test, (int) $validated['disc_question_id'])) {
            return back()->withErrors([
                'p' => 'Nomor soal yang sedang dibuka belum terisi lengkap.',
            ])->withInput();
        }

        if ($hasCompleteAnswer) {
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
        }

        if ($action === 'finish') {
            if ($test->answers()->count() < self::TOTAL_QUESTIONS) {
                return back()->withErrors([
                    'p' => 'Tes hanya bisa diselesaikan setelah semua nomor terisi.',
                ])->withInput();
            }

            $test->update(['submitted_at' => now()]);

            return redirect("/test/{$test->id}/result");
        }

        $target = $this->resolveTargetNumber(
            (int) $validated['question_number'],
            $action,
            isset($validated['target_number']) ? (int) $validated['target_number'] : null,
            self::TOTAL_QUESTIONS
        );

        return redirect("/test/{$test->id}/question/{$target}");
    }

    private function remainingSeconds(DiscTest $test): int
    {
        if (!$test->started_at) {
            return self::TIME_LIMIT_MINUTES * 60;
        }

        $elapsed = max(0, now()->timestamp - $test->started_at->timestamp);

        return max(0, self::TIME_LIMIT_MINUTES * 60 - $elapsed);
    }

    private function isTimeExpired(DiscTest $test): bool
    {
        return $this->remainingSeconds($test) <= 0;
    }

    private function hasSavedAnswer(DiscTest $test, int $questionId): bool
    {
        return DiscAnswer::where('disc_test_id', $test->id)
            ->where('disc_question_id', $questionId)
            ->exists();
    }

    private function resolveTargetNumber(int $currentNumber, string $action, ?int $targetNumber, int $totalQuestions): int
    {
        return match ($action) {
            'prev' => max(1, $currentNumber - 1),
            'goto' => min($totalQuestions, max(1, $targetNumber ?? $currentNumber)),
            default => min($totalQuestions, $currentNumber + 1),
        };
    }
}
