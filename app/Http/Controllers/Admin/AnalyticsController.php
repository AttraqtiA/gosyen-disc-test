<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscQuestion;
use App\Models\DiscTest;
use App\Models\MbtiTest;
use App\Models\OceanTest;
use App\Models\TestSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        [$startDate, $endDate, $activePreset] = $this->resolveDateRange($request);
        $user = $request->user();

        $sessions = TestSession::with('client')
            ->when($user->isClientAdmin(), fn ($q) => $q->where('client_id', $user->client_id))
            ->latest()
            ->get();

        $rows = $sessions->map(function (TestSession $session) use ($startDate, $endDate) {
            $stats = $this->sessionStats($session, $startDate, $endDate);

            return [
                'session' => $session,
                ...$stats,
            ];
        });

        $summary = [
            'total_sessions' => $rows->count(),
            'total_started' => (int) $rows->sum('started'),
            'total_completed' => (int) $rows->sum('completed'),
            'total_timeout' => (int) $rows->sum('timeout'),
            'total_in_progress' => (int) $rows->sum('in_progress'),
        ];

        $summary['completion_rate'] = $summary['total_started'] > 0
            ? round(($summary['total_completed'] / $summary['total_started']) * 100, 2)
            : 0.0;

        $trend = $this->dailyTrend($startDate, $endDate, $user->isSuperAdmin() ? null : $user->client_id);

        return view('admin.analytics.index', [
            'rows' => $rows,
            'summary' => $summary,
            'trend' => $trend,
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'preset' => $activePreset,
            ],
            'queryString' => http_build_query([
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'preset' => $activePreset,
            ]),
        ]);
    }

    public function exportAll(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $user = $request->user();
        $clientId = $user->isSuperAdmin() ? null : $user->client_id;

        $type = strtoupper((string) $request->query('type', 'ALL'));
        $filename = 'export-hasil-' . strtolower($type) . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($type, $startDate, $endDate, $clientId) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $this->csvHeader());

            foreach ($this->buildExportRows($type, null, $startDate, $endDate, $clientId) as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportSession(Request $request, TestSession $session): StreamedResponse
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && (int) $session->client_id !== (int) $user->client_id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        [$startDate, $endDate] = $this->resolveDateRange($request);
        $type = strtoupper($session->test_type);
        $filename = 'export-sesi-' . strtolower($session->code) . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($session, $type, $startDate, $endDate, $user) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $this->csvHeader());

            foreach ($this->buildExportRows($type, $session->id, $startDate, $endDate, $user->isSuperAdmin() ? null : $user->client_id) as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportDiscQuestions(): StreamedResponse
    {
        $filename = 'disc-bank-soal-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'no_soal',
                'pernyataan_1',
                'tipe_1',
                'pernyataan_2',
                'tipe_2',
                'pernyataan_3',
                'tipe_3',
                'pernyataan_4',
                'tipe_4',
            ]);

            DiscQuestion::with('statements')
                ->orderBy('question_number')
                ->get()
                ->each(function (DiscQuestion $question) use ($out) {
                    $statements = $question->statements->sortBy('id')->values();

                    $row = [$question->question_number];
                    for ($i = 0; $i < 4; $i++) {
                        $row[] = $statements[$i]->text ?? '';
                        $row[] = $statements[$i]->disc_type ?? '';
                    }

                    fputcsv($out, $row);
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportDiscManual(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $validated = $request->validate([
            'session_id' => ['nullable', 'integer', 'exists:test_sessions,id'],
        ]);

        $user = $request->user();
        $filename = 'disc-jawaban-manual-' . now()->format('Ymd-His') . '.csv';
        $sessionId = $validated['session_id'] ?? null;

        $query = DiscTest::with([
            'session',
            'answers.question',
            'answers.pStatement',
            'answers.kStatement',
        ])->orderBy('started_at');

        if ($sessionId) {
            $query->where('test_session_id', $sessionId);
        }
        if (!$user->isSuperAdmin()) {
            $query->where('client_id', $user->client_id);
        }

        $this->applyDateRange($query, $startDate, $endDate);
        $tests = $query->get();

        return response()->streamDownload(function () use ($tests) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, $this->discManualHeader());

            foreach ($tests as $test) {
                fputcsv($out, $this->discManualRow($test));
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'preset' => ['nullable', 'in:7d,30d,this_month,custom'],
        ]);

        $preset = $validated['preset'] ?? null;
        $activePreset = 'custom';

        if ($preset === '7d') {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
            $activePreset = '7d';
        } elseif ($preset === '30d') {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
            $activePreset = '30d';
        } elseif ($preset === 'this_month') {
            $startDate = now()->startOfMonth()->startOfDay();
            $endDate = now()->endOfDay();
            $activePreset = 'this_month';
        } else {
            $startDate = isset($validated['start_date'])
                ? Carbon::parse($validated['start_date'])->startOfDay()
                : now()->subDays(29)->startOfDay();

            $endDate = isset($validated['end_date'])
                ? Carbon::parse($validated['end_date'])->endOfDay()
                : now()->endOfDay();
        }

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        return [$startDate, $endDate, $activePreset];
    }

    private function sessionStats(TestSession $session, Carbon $startDate, Carbon $endDate): array
    {
        [$modelClass, $timeLimitMinutes] = $this->resolver($session->test_type);

        if (!$modelClass || !$timeLimitMinutes) {
            return [
                'started' => 0,
                'completed' => 0,
                'timeout' => 0,
                'in_progress' => 0,
                'avg_duration_seconds' => null,
            ];
        }

        $base = $modelClass::query()->where('test_session_id', $session->id);

        $started = (clone $base)
            ->whereNotNull('started_at')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->count();

        $completed = (clone $base)
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->count();

        $timeoutThreshold = now()->subMinutes($timeLimitMinutes);
        $timeout = (clone $base)
            ->whereNull('submitted_at')
            ->whereNotNull('started_at')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->where('started_at', '<=', $timeoutThreshold)
            ->count();

        $inProgress = (clone $base)
            ->whereNull('submitted_at')
            ->whereNotNull('started_at')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->where('started_at', '>', $timeoutThreshold)
            ->count();

        $avgDurationSeconds = (clone $base)
            ->whereNotNull('started_at')
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->get(['started_at', 'submitted_at'])
            ->avg(function ($item) {
                return Carbon::parse($item->submitted_at)->diffInSeconds(Carbon::parse($item->started_at));
            });

        return [
            'started' => $started,
            'completed' => $completed,
            'timeout' => $timeout,
            'in_progress' => $inProgress,
            'avg_duration_seconds' => $avgDurationSeconds !== null ? (int) round($avgDurationSeconds) : null,
        ];
    }

    private function dailyTrend(Carbon $startDate, Carbon $endDate, ?int $clientId = null): array
    {
        $started = [];
        $completed = [];
        $timeout = [];

        $cursor = $startDate->copy()->startOfDay();
        while ($cursor->lte($endDate)) {
            $key = $cursor->toDateString();
            $started[$key] = 0;
            $completed[$key] = 0;
            $timeout[$key] = 0;
            $cursor->addDay();
        }

        $this->fillTrendForModel(DiscTest::class, 15, $startDate, $endDate, $started, $completed, $timeout, $clientId);
        $this->fillTrendForModel(MbtiTest::class, 12, $startDate, $endDate, $started, $completed, $timeout, $clientId);
        $this->fillTrendForModel(OceanTest::class, 10, $startDate, $endDate, $started, $completed, $timeout, $clientId);

        return [
            'labels' => array_keys($started),
            'started' => array_values($started),
            'completed' => array_values($completed),
            'timeout' => array_values($timeout),
        ];
    }

    private function fillTrendForModel(
        string $modelClass,
        int $limitMinutes,
        Carbon $startDate,
        Carbon $endDate,
        array &$started,
        array &$completed,
        array &$timeout,
        ?int $clientId = null
    ): void {
        $query = $modelClass::query()
            ->whereNotNull('started_at')
            ->whereBetween('started_at', [$startDate, $endDate]);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $tests = $query->get(['started_at', 'submitted_at']);

        $timeoutThreshold = now()->subMinutes($limitMinutes);

        foreach ($tests as $test) {
            $startedKey = Carbon::parse($test->started_at)->toDateString();
            if (array_key_exists($startedKey, $started)) {
                $started[$startedKey]++;
            }

            if ($test->submitted_at) {
                $completedKey = Carbon::parse($test->submitted_at)->toDateString();
                if (array_key_exists($completedKey, $completed)) {
                    $completed[$completedKey]++;
                }
                continue;
            }

            if (Carbon::parse($test->started_at)->lte($timeoutThreshold) && array_key_exists($startedKey, $timeout)) {
                $timeout[$startedKey]++;
            }
        }
    }

    private function buildExportRows(
        string $type = 'ALL',
        ?int $sessionId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        ?int $clientId = null
    ): array {
        $rows = [];

        if ($type === 'ALL' || $type === 'DISC') {
            $query = DiscTest::with(['session', 'client', 'result', 'recommendations.position']);
            if ($sessionId) {
                $query->where('test_session_id', $sessionId);
            }
            if ($clientId) {
                $query->where('client_id', $clientId);
            }
            $this->applyDateRange($query, $startDate, $endDate);

            foreach ($query->get() as $test) {
                $top = $test->recommendations->sortBy('rank')->first();
                $duration = $this->durationSeconds($test->started_at, $test->submitted_at);

                $rows[] = [
                    $test->session?->code,
                    $test->session?->name,
                    'DISC',
                    $test->id,
                    $test->nama,
                    $test->email,
                    $test->nomor_hp,
                    $test->institusi_perusahaan,
                    $test->departemen_divisi,
                    $test->jabatan_saat_ini,
                    $test->usia,
                    $test->jenis_kelamin,
                    $test->pendidikan_terakhir,
                    $test->lama_pengalaman_kerja,
                    $test->lokasi_kota,
                    $test->tujuan_tes,
                    optional($test->tanggal_tes)->format('Y-m-d'),
                    optional($test->started_at)->format('Y-m-d H:i:s'),
                    optional($test->submitted_at)->format('Y-m-d H:i:s'),
                    $duration,
                    $this->statusLabel($test->submitted_at, $test->started_at, 15),
                    $test->result ? sprintf('D=%s;I=%s;S=%s;C=%s;Dominan=%s', $test->result->d_score, $test->result->i_score, $test->result->s_score, $test->result->c_score, $test->result->dominant_type) : '',
                    $top?->position?->title,
                    $top?->match_score,
                ];
            }
        }

        if ($type === 'ALL' || $type === 'MBTI') {
            $query = MbtiTest::with(['session', 'client', 'result', 'recommendations.position']);
            if ($sessionId) {
                $query->where('test_session_id', $sessionId);
            }
            if ($clientId) {
                $query->where('client_id', $clientId);
            }
            $this->applyDateRange($query, $startDate, $endDate);

            foreach ($query->get() as $test) {
                $top = $test->recommendations->sortBy('rank')->first();
                $duration = $this->durationSeconds($test->started_at, $test->submitted_at);

                $rows[] = [
                    $test->session?->code,
                    $test->session?->name,
                    'MBTI',
                    $test->id,
                    $test->nama,
                    $test->email,
                    $test->nomor_hp,
                    $test->institusi_perusahaan,
                    $test->departemen_divisi,
                    $test->jabatan_saat_ini,
                    $test->usia,
                    $test->jenis_kelamin,
                    $test->pendidikan_terakhir,
                    $test->lama_pengalaman_kerja,
                    $test->lokasi_kota,
                    $test->tujuan_tes,
                    optional($test->tanggal_tes)->format('Y-m-d'),
                    optional($test->started_at)->format('Y-m-d H:i:s'),
                    optional($test->submitted_at)->format('Y-m-d H:i:s'),
                    $duration,
                    $this->statusLabel($test->submitted_at, $test->started_at, 12),
                    $test->result ? sprintf('Type=%s;E=%s;I=%s;S=%s;N=%s;T=%s;F=%s;J=%s;P=%s', $test->result->type_code, $test->result->e_score, $test->result->i_score, $test->result->s_score, $test->result->n_score, $test->result->t_score, $test->result->f_score, $test->result->j_score, $test->result->p_score) : '',
                    $top?->position?->title,
                    $top?->match_score,
                ];
            }
        }

        if ($type === 'ALL' || $type === 'OCEAN') {
            $query = OceanTest::with(['session', 'client', 'result', 'recommendations.position']);
            if ($sessionId) {
                $query->where('test_session_id', $sessionId);
            }
            if ($clientId) {
                $query->where('client_id', $clientId);
            }
            $this->applyDateRange($query, $startDate, $endDate);

            foreach ($query->get() as $test) {
                $top = $test->recommendations->sortBy('rank')->first();
                $duration = $this->durationSeconds($test->started_at, $test->submitted_at);

                $rows[] = [
                    $test->session?->code,
                    $test->session?->name,
                    'OCEAN',
                    $test->id,
                    $test->nama,
                    $test->email,
                    $test->nomor_hp,
                    $test->institusi_perusahaan,
                    $test->departemen_divisi,
                    $test->jabatan_saat_ini,
                    $test->usia,
                    $test->jenis_kelamin,
                    $test->pendidikan_terakhir,
                    $test->lama_pengalaman_kerja,
                    $test->lokasi_kota,
                    $test->tujuan_tes,
                    optional($test->tanggal_tes)->format('Y-m-d'),
                    optional($test->started_at)->format('Y-m-d H:i:s'),
                    optional($test->submitted_at)->format('Y-m-d H:i:s'),
                    $duration,
                    $this->statusLabel($test->submitted_at, $test->started_at, 10),
                    $test->result ? sprintf('Dominan=%s;O=%s;C=%s;E=%s;A=%s;N=%s', $test->result->dominant_trait, $test->result->o_score, $test->result->c_score, $test->result->e_score, $test->result->a_score, $test->result->n_score) : '',
                    $top?->position?->title,
                    $top?->match_score,
                ];
            }
        }

        return $rows;
    }

    private function applyDateRange(Builder $query, ?Carbon $startDate, ?Carbon $endDate): void
    {
        if ($startDate && $endDate) {
            $query->whereNotNull('started_at')->whereBetween('started_at', [$startDate, $endDate]);
        }
    }

    private function csvHeader(): array
    {
        return [
            'session_code',
            'session_name',
            'test_type',
            'test_id',
            'nama',
            'email',
            'nomor_hp',
            'institusi_perusahaan',
            'departemen_divisi',
            'jabatan_saat_ini',
            'usia',
            'jenis_kelamin',
            'pendidikan_terakhir',
            'lama_pengalaman_kerja',
            'lokasi_kota',
            'tujuan_tes',
            'tanggal_tes',
            'started_at',
            'submitted_at',
            'duration_seconds',
            'status',
            'result_summary',
            'top_recommendation_position',
            'top_recommendation_score',
        ];
    }

    private function statusLabel($submittedAt, $startedAt, int $limitMinutes): string
    {
        if ($submittedAt) {
            return 'COMPLETED';
        }

        if ($startedAt && Carbon::parse($startedAt)->lte(now()->subMinutes($limitMinutes))) {
            return 'TIMEOUT';
        }

        if ($startedAt) {
            return 'IN_PROGRESS';
        }

        return 'NOT_STARTED';
    }

    private function durationSeconds($startedAt, $submittedAt): ?int
    {
        if (!$startedAt || !$submittedAt) {
            return null;
        }

        return Carbon::parse($submittedAt)->diffInSeconds(Carbon::parse($startedAt));
    }

    private function resolver(string $testType): array
    {
        return match (strtoupper($testType)) {
            'DISC' => [DiscTest::class, 15],
            'MBTI' => [MbtiTest::class, 12],
            'OCEAN' => [OceanTest::class, 10],
            default => [null, null],
        };
    }

    private function discManualHeader(): array
    {
        $header = [
            'session_code',
            'session_name',
            'test_id',
            'nama',
            'institusi_perusahaan',
            'departemen_divisi',
            'jabatan_saat_ini',
            'usia',
            'jenis_kelamin',
            'started_at',
            'submitted_at',
            'status',
            'jumlah_terjawab',
        ];

        for ($i = 1; $i <= 24; $i++) {
            $key = 'q' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $header[] = "{$key}_p_tipe";
            $header[] = "{$key}_p_pernyataan";
            $header[] = "{$key}_k_tipe";
            $header[] = "{$key}_k_pernyataan";
        }

        $header[] = 'd_total_manual';
        $header[] = 'i_total_manual';
        $header[] = 's_total_manual';
        $header[] = 'c_total_manual';
        $header[] = 'tipe_dominan_manual';

        return $header;
    }

    private function discManualRow(DiscTest $test): array
    {
        $answersByNumber = $test->answers
            ->filter(fn ($answer) => $answer->question)
            ->keyBy(fn ($answer) => (int) $answer->question->question_number);

        $scores = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
        $answeredCount = 0;

        $identity = [
            $test->session?->code,
            $test->session?->name,
            $test->id,
            $test->nama,
            $test->institusi_perusahaan,
            $test->departemen_divisi,
            $test->jabatan_saat_ini,
            $test->usia,
            $test->jenis_kelamin,
            optional($test->started_at)->format('Y-m-d H:i:s'),
            optional($test->submitted_at)->format('Y-m-d H:i:s'),
            $this->statusLabel($test->submitted_at, $test->started_at, 15),
        ];
        $questionColumns = [];

        for ($i = 1; $i <= 24; $i++) {
            $answer = $answersByNumber->get($i);
            $pType = $answer?->pStatement?->disc_type;
            $kType = $answer?->kStatement?->disc_type;
            $pText = $answer?->pStatement?->text;
            $kText = $answer?->kStatement?->text;

            if ($answer) {
                $answeredCount++;
            }

            if (isset($scores[$pType])) {
                $scores[$pType]++;
            }
            if (isset($scores[$kType])) {
                $scores[$kType]--;
            }

            $questionColumns[] = $pType;
            $questionColumns[] = $pText;
            $questionColumns[] = $kType;
            $questionColumns[] = $kText;
        }

        $max = max($scores);
        $dominants = array_keys(array_filter($scores, fn ($value) => $value === $max));

        return [
            ...$identity,
            $answeredCount,
            ...$questionColumns,
            $scores['D'],
            $scores['I'],
            $scores['S'],
            $scores['C'],
            implode('/', $dominants),
        ];
    }
}
