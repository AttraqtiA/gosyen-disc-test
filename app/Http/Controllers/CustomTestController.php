<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CustomTest;
use App\Models\CustomTestQuestion;
use App\Models\CustomTestSubmission;
use App\Models\CustomTestSubmissionAnswer;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomTestController extends Controller
{
    public function start(string $code)
    {
        $session = $this->resolveSession($code);
        $items = $this->sessionItems($session);

        if ($items->isEmpty()) {
            abort(404, 'Belum ada custom test yang ditautkan ke kode sesi ini.');
        }

        return view('custom.start', [
            'session' => $session,
            'items' => $items,
        ]);
    }

    public function storeMeta(Request $request)
    {
        $validated = $request->validate([
            'access_code' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
            'institusi_perusahaan' => ['nullable', 'string', 'max:255'],
            'departemen_divisi' => ['nullable', 'string', 'max:255'],
            'jabatan_saat_ini' => ['nullable', 'string', 'max:255'],
        ]);

        $session = $this->resolveSession($validated['access_code']);
        $items = $this->sessionItems($session);

        if ($items->isEmpty()) {
            return redirect('/')->withErrors([
                'code' => 'Custom test pada kode ini belum disiapkan.',
            ]);
        }

        $clientId = null;

        if (Schema::hasTable('clients') && $session->client_id) {
            $clientId = $session->client_id;
        } elseif (Schema::hasTable('clients') && !empty($validated['institusi_perusahaan'])) {
            $client = Client::firstOrCreate(
                ['name' => $validated['institusi_perusahaan']],
                ['code' => Str::slug($validated['institusi_perusahaan']) . '-' . Str::lower(Str::random(5))]
            );

            $clientId = $client->id;
        }

        $submission = CustomTestSubmission::create([
            'custom_test_id' => $items->first()->id,
            'test_session_id' => $session->id,
            'packet_attempt_uuid' => (string) Str::uuid(),
            'packet_index' => 1,
            'packet_size' => $items->count(),
            'client_id' => $clientId,
            'nama' => $validated['nama'],
            'email' => $validated['email'] ?? null,
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'institusi_perusahaan' => $validated['institusi_perusahaan'] ?? null,
            'departemen_divisi' => $validated['departemen_divisi'] ?? null,
            'jabatan_saat_ini' => $validated['jabatan_saat_ini'] ?? null,
            'started_at' => now(),
        ]);

        return redirect("/custom/test/{$submission->id}/question/1");
    }

    public function question(CustomTestSubmission $submission, int $number)
    {
        $submission->loadMissing(['customTest.questions.options', 'session.customTestItems.customTest']);
        $questions = $submission->customTest->questions;
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0 || $number < 1 || $number > $totalQuestions) {
            abort(404);
        }

        if ($this->isTimeExpired($submission)) {
            return redirect("/custom/test/{$submission->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang sudah tersimpan tetap diproses.');
        }

        $question = $questions->values()->get($number - 1);
        $existingAnswer = $submission->answers()
            ->where('custom_test_question_id', $question->id)
            ->first();
        $answeredQuestionIds = $submission->answers()->pluck('custom_test_question_id');
        $answeredNumbers = $questions
            ->values()
            ->map(fn ($item, $index) => $answeredQuestionIds->contains($item->id) ? $index + 1 : null)
            ->filter()
            ->values();

        return view('custom.question', [
            'submission' => $submission,
            'question' => $question,
            'number' => $number,
            'totalQuestions' => $totalQuestions,
            'existingAnswer' => $existingAnswer,
            'remainingSeconds' => $this->remainingSeconds($submission),
            'answeredNumbers' => $answeredNumbers,
            'packetItems' => $this->sessionItems($submission->session),
        ]);
    }

    public function answer(Request $request, CustomTestSubmission $submission)
    {
        $submission->loadMissing(['customTest.questions.options', 'session.customTestItems.customTest']);

        if ($this->isTimeExpired($submission)) {
            return redirect("/custom/test/{$submission->id}/result")
                ->with('warning', 'Waktu pengerjaan habis. Jawaban yang sudah tersimpan tetap diproses.');
        }

        $questions = $submission->customTest->questions->values();
        $totalQuestions = $questions->count();

        $validated = $request->validate([
            'custom_test_question_id' => ['required', 'exists:custom_test_questions,id'],
            'question_number' => ['required', 'integer', 'min:1', 'max:' . max(1, $totalQuestions)],
            'custom_test_option_id' => ['nullable', 'exists:custom_test_options,id'],
            'answer_text' => ['nullable', 'string', 'max:5000'],
            'action' => ['nullable', 'in:prev,next,goto,finish'],
            'target_number' => ['nullable', 'integer', 'min:1', 'max:' . max(1, $totalQuestions)],
        ]);

        $question = $questions->firstWhere('id', (int) $validated['custom_test_question_id']);
        if (!$question) {
            abort(422, 'Pertanyaan tidak cocok dengan test ini.');
        }

        $action = $validated['action'] ?? 'next';
        $selectedOptionId = $validated['custom_test_option_id'] ?? null;
        $answerText = trim((string) ($validated['answer_text'] ?? ''));

        if ($question->question_type === 'essay') {
            if ($question->is_required && $action !== 'prev' && $action !== 'goto' && $answerText === '' && !$this->hasSavedAnswer($submission, $question->id)) {
                return back()->withErrors([
                    'answer_text' => 'Jawaban untuk nomor ini masih kosong.',
                ])->withInput();
            }
        } else {
            $option = $question->options->firstWhere('id', $selectedOptionId);

            if (!$option && $question->is_required && $action !== 'prev' && $action !== 'goto' && !$this->hasSavedAnswer($submission, $question->id)) {
                return back()->withErrors([
                    'custom_test_option_id' => 'Pilih salah satu jawaban terlebih dahulu.',
                ])->withInput();
            }
        }

        if ($selectedOptionId || $answerText !== '') {
            $option = $question->options->firstWhere('id', $selectedOptionId);

            if ($selectedOptionId && !$option) {
                return back()->withErrors([
                    'custom_test_option_id' => 'Opsi tidak valid untuk nomor ini.',
                ])->withInput();
            }

            CustomTestSubmissionAnswer::updateOrCreate(
                [
                    'custom_test_submission_id' => $submission->id,
                    'custom_test_question_id' => $question->id,
                ],
                [
                    'custom_test_option_id' => $option?->id,
                    'answer_text' => $question->question_type === 'essay' ? $answerText : null,
                    'auto_scores_json' => $option?->scores_json,
                    'review_status' => $question->question_type === 'essay' ? 'pending_review' : 'reviewed',
                ]
            );
        }

        if ($action === 'finish') {
            $missingRequired = $questions->filter(function ($item) use ($submission) {
                if (!$item->is_required) {
                    return false;
                }

                return !$this->hasSavedAnswer($submission, $item->id);
            });

            if ($missingRequired->isNotEmpty()) {
                $firstMissing = $questions->search(fn ($item) => $item->id === $missingRequired->first()->id);

                return redirect("/custom/test/{$submission->id}/question/" . ($firstMissing + 1))
                    ->withErrors(['answer_text' => 'Masih ada soal wajib yang belum dijawab.']);
            }

            $hasEssay = $questions->contains(fn ($item) => $item->question_type === 'essay');
            $submission->update([
                'submitted_at' => now(),
                'review_status' => $hasEssay ? 'pending_review' : 'reviewed',
            ]);

            $nextSubmission = $this->createNextSubmissionIfNeeded($submission);
            if ($nextSubmission) {
                return redirect("/custom/test/{$nextSubmission->id}/question/1");
            }

            return redirect("/custom/test/{$submission->id}/result");
        }

        $target = $this->resolveTargetNumber(
            (int) $validated['question_number'],
            $action,
            isset($validated['target_number']) ? (int) $validated['target_number'] : null,
            $totalQuestions
        );

        return redirect("/custom/test/{$submission->id}/question/{$target}");
    }

    public function result(CustomTestSubmission $submission)
    {
        $submission->loadMissing(['customTest.dimensions', 'customTest.questions', 'answers.question', 'session.customTestItems.customTest']);
        $packetSubmissions = $this->packetSubmissions($submission);
        $packetSummary = $packetSubmissions->map(function (CustomTestSubmission $item) {
            $rawScore = $item->answers->sum(function ($answer) {
                return array_sum($answer->auto_scores_json ?? []);
            });

            return [
                'submission' => $item,
                'raw_score' => $rawScore,
                'answered' => $item->answers->count(),
                'total_questions' => $item->customTest->questions->count(),
            ];
        });

        $completedCount = $packetSubmissions->whereNotNull('submitted_at')->count();
        $packetComplete = $completedCount === $packetSubmissions->count();
        $totalRaw = $packetSummary->sum('raw_score');
        $averageRaw = $packetSummary->count() > 0 ? round($totalRaw / $packetSummary->count(), 2) : 0;

        return view('custom.result', [
            'submission' => $submission,
            'packetItems' => $this->sessionItems($submission->session),
            'packetSummary' => $packetSummary,
            'packetComplete' => $packetComplete,
            'totalRaw' => $totalRaw,
            'averageRaw' => $averageRaw,
        ]);
    }

    private function resolveSession(string $code): TestSession
    {
        return TestSession::query()
            ->with('customTestItems.customTest')
            ->whereRaw('UPPER(code) = ?', [Str::upper($code)])
            ->where('test_type', 'CUSTOM')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
    }

    private function sessionItems(TestSession $session): Collection
    {
        return $session->customTestItems
            ->sortBy('sort_order')
            ->pluck('customTest')
            ->filter(fn ($test) => $test && $test->is_active)
            ->values();
    }

    private function remainingSeconds(CustomTestSubmission $submission): int
    {
        $minutes = (int) ($submission->customTest->time_limit_minutes ?? 0);
        if ($minutes <= 0 || !$submission->started_at) {
            return PHP_INT_MAX;
        }

        $elapsed = max(0, now()->timestamp - $submission->started_at->timestamp);

        return max(0, ($minutes * 60) - $elapsed);
    }

    private function isTimeExpired(CustomTestSubmission $submission): bool
    {
        return $this->remainingSeconds($submission) === 0;
    }

    private function hasSavedAnswer(CustomTestSubmission $submission, int $questionId): bool
    {
        return $submission->answers()->where('custom_test_question_id', $questionId)->exists();
    }

    private function createNextSubmissionIfNeeded(CustomTestSubmission $submission): ?CustomTestSubmission
    {
        $items = $this->sessionItems($submission->session);
        $nextIndex = $submission->packet_index + 1;
        $nextTest = $items->get($nextIndex - 1);

        if (!$nextTest) {
            return null;
        }

        return CustomTestSubmission::firstOrCreate(
            [
                'test_session_id' => $submission->test_session_id,
                'custom_test_id' => $nextTest->id,
                'packet_attempt_uuid' => $submission->packet_attempt_uuid,
            ],
            [
                'packet_index' => $nextIndex,
                'packet_size' => $items->count(),
                'client_id' => $submission->client_id,
                'nama' => $submission->nama,
                'email' => $submission->email,
                'nomor_hp' => $submission->nomor_hp,
                'institusi_perusahaan' => $submission->institusi_perusahaan,
                'departemen_divisi' => $submission->departemen_divisi,
                'jabatan_saat_ini' => $submission->jabatan_saat_ini,
                'started_at' => now(),
            ]
        );
    }

    private function packetSubmissions(CustomTestSubmission $submission): Collection
    {
        return CustomTestSubmission::query()
            ->with(['customTest.questions', 'answers'])
            ->where('test_session_id', $submission->test_session_id)
            ->where('packet_attempt_uuid', $submission->packet_attempt_uuid)
            ->orderBy('packet_index')
            ->get();
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
