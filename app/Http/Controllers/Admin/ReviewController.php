<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTestSubmissionAnswer;
use App\Models\TestSession;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $sessionQuery = TestSession::query()
            ->with('client')
            ->where('is_active', true)
            ->whereHas('customTestSubmissions.answers.question', function ($q) {
                $q->where('question_type', 'essay');
            });

        if (!$user->isSuperAdmin()) {
            $sessionQuery->where('client_id', $user->client_id);
        }

        $sessions = $sessionQuery->latest()->get();

        $sessionIds = $sessions->pluck('id')->all();
        $selectedSessionId = (int) $request->query('session_id', $sessions->first()?->id);
        if (!in_array($selectedSessionId, $sessionIds, true)) {
            $selectedSessionId = $sessions->first()?->id;
        }

        $status = $request->query('status', 'pending_review');
        if (!in_array($status, ['pending_review', 'reviewed'], true)) {
            $status = 'pending_review';
        }

        $sessions = $sessions->map(function (TestSession $session) use ($user) {
            $base = CustomTestSubmissionAnswer::query()
                ->whereHas('question', fn ($q) => $q->where('question_type', 'essay'))
                ->whereHas('submission', function ($sq) use ($session, $user) {
                    $sq->where('test_session_id', $session->id);
                    if (!$user->isSuperAdmin()) {
                        $sq->where('client_id', $user->client_id);
                    }
                });

            $session->pending_count = (clone $base)->where('review_status', 'pending_review')->count();
            $session->reviewed_count = (clone $base)->where('review_status', 'reviewed')->count();

            return $session;
        });

        $query = CustomTestSubmissionAnswer::query()
            ->with([
                'question.test',
                'submission.client',
                'submission.session',
                'reviewer',
            ])
            ->whereHas('question', function ($q) {
                $q->where('question_type', 'essay');
            })
            ->where('review_status', $status);

        if ($selectedSessionId) {
            $query->whereHas('submission', function ($sq) use ($selectedSessionId, $user) {
                $sq->where('test_session_id', $selectedSessionId);
                if (!$user->isSuperAdmin()) {
                    $sq->where('client_id', $user->client_id);
                }
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $items = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', [
            'items' => $items,
            'status' => $status,
            'sessions' => $sessions,
            'selectedSessionId' => $selectedSessionId,
        ]);
    }

    public function update(Request $request, CustomTestSubmissionAnswer $answer)
    {
        $user = $request->user();

        $answer->loadMissing(['question', 'submission']);

        if ($answer->question->question_type !== 'essay') {
            abort(422, 'Hanya jawaban essay yang bisa direview manual.');
        }

        if (!$user->isSuperAdmin() && $answer->submission?->client_id !== $user->client_id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $validated = $request->validate([
            'reviewer_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'reviewer_notes' => ['nullable', 'string', 'max:5000'],
            'review_status' => ['required', 'in:pending_review,reviewed'],
        ]);

        $answer->update([
            'reviewer_score' => $validated['reviewer_score'] ?? null,
            'reviewer_notes' => $validated['reviewer_notes'] ?? null,
            'review_status' => $validated['review_status'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $submission = $answer->submission;
        if ($submission) {
            $pending = $submission->answers()
                ->whereHas('question', fn ($q) => $q->where('question_type', 'essay'))
                ->where('review_status', 'pending_review')
                ->exists();

            $submission->update([
                'review_status' => $pending ? 'pending_review' : 'reviewed',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
        }

        return back()->with('success', 'Review jawaban essay berhasil diperbarui.');
    }
}
