<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tes MBTI</title>
    <style>
        :root { --bg:#030b1d; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --accent:#5ce8ff; --accent-2:#35d0f5; }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; font-family:Arial,sans-serif; color:var(--text); background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg); padding:32px 16px; }
        .container { max-width:860px; margin:0 auto; background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98)); border:1px solid var(--line); border-radius:16px; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,.35); }
        h1 { margin-top:0; }
        .card { margin-top:14px; padding:14px; border:1px solid var(--line); border-radius:10px; background:#071327; font-size:14px; line-height:1.6; }
        .muted { color:var(--muted); }
        .warning { margin-top:10px; padding:10px 12px; border:1px solid rgba(92,232,255,.5); background:rgba(92,232,255,.12); color:#bff7ff; border-radius:8px; }
        .type-chip { display:inline-block; margin-top:8px; padding:7px 12px; border-radius:999px; background:rgba(92,232,255,.12); border:1px solid rgba(92,232,255,.35); color:#bff7ff; font-weight:800; }
        a.btn { display:inline-block; margin-top:18px; text-decoration:none; color:#032137; font-weight:800; background:linear-gradient(180deg,var(--accent),var(--accent-2)); border-radius:10px; padding:10px 14px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Hasil Tes Kepribadian MBTI</h1>
    <p class="muted">Terima kasih, {{ $test->nama }}.</p>
    <p><a class="btn" href="/handbook?type=MBTI">Lihat Panduan MBTI</a></p>

    @if (session('warning'))
        <div class="warning">{{ session('warning') }}</div>
    @endif

    @php
        $pair = function ($a, $b) {
            $sum = $a + $b;
            if ($sum <= 0) return [50.0, 50.0];
            return [round(($a / $sum) * 100, 2), round(($b / $sum) * 100, 2)];
        };
        [$ePct, $iPct] = $pair($result->e_score, $result->i_score);
        [$sPct, $nPct] = $pair($result->s_score, $result->n_score);
        [$tPct, $fPct] = $pair($result->t_score, $result->f_score);
        [$jPct, $pPct] = $pair($result->j_score, $result->p_score);
    @endphp

    <div class="card">
        <div><strong>Status Jawaban:</strong> {{ $answered }} / {{ $totalQuestions }} nomor terisi.</div>
        <div><strong>Institusi:</strong> {{ $test->institusi_perusahaan }}</div>
        <div><strong>Departemen/Divisi:</strong> {{ $test->departemen_divisi }}</div>
        <div><strong>Tanggal Tes:</strong> {{ $test->tanggal_tes?->format('d-m-Y') }}</div>
        <div class="type-chip">Tipe MBTI: {{ $result->type_code ?: '-' }}</div>
    </div>

    <div class="card">
        <strong>Distribusi Dimensi</strong>
        <div style="margin-top:8px;">E {{ $result->e_score }} ({{ $ePct }}%) vs I {{ $result->i_score }} ({{ $iPct }}%)</div>
        <div>S {{ $result->s_score }} ({{ $sPct }}%) vs N {{ $result->n_score }} ({{ $nPct }}%)</div>
        <div>T {{ $result->t_score }} ({{ $tPct }}%) vs F {{ $result->f_score }} ({{ $fPct }}%)</div>
        <div>J {{ $result->j_score }} ({{ $jPct }}%) vs P {{ $result->p_score }} ({{ $pPct }}%)</div>
    </div>

    <div class="card">
        <strong>Rekomendasi Posisi (Deterministic Matching)</strong>
        @if ($recommendations->isEmpty())
            <div class="muted" style="margin-top:8px;">Belum ada benchmark posisi MBTI aktif untuk client ini.</div>
        @else
            <ol style="margin-top:8px; padding-left:18px;">
                @foreach ($recommendations->take(5) as $rec)
                    @php
                        $clientNames = $hasClientPosition ? $rec->position->clients->pluck('name')->implode(', ') : '';
                        $fallbackClient = $rec->position->client->name ?? '-';
                        $displayClient = $clientNames !== '' ? $clientNames : $fallbackClient;
                    @endphp
                    <li>{{ $rec->position->title }} ({{ $displayClient }}) - Skor: {{ number_format($rec->match_score, 2) }}</li>
                @endforeach
            </ol>
        @endif
    </div>

    <a class="btn" href="/">Mulai Tes Baru</a>
</div>
</body>
</html>
