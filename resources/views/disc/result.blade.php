<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tes Kepribadian</title>
    <style>
        :root {
            --bg: #030b1d;
            --panel: #08152d;
            --panel-soft: #0d2042;
            --line: #1f3d68;
            --text: #e6f1ff;
            --muted: #94acd0;
            --accent: #5ce8ff;
            --accent-2: #35d0f5;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% 18%, rgba(73, 224, 255, 0.18), transparent 38%),
                radial-gradient(circle at 90% 12%, rgba(73, 126, 255, 0.18), transparent 34%),
                var(--bg);
            padding: 32px 16px;
        }

        .container {
            max-width: 780px;
            margin: 0 auto;
            background: linear-gradient(180deg, rgba(13, 32, 66, 0.96), rgba(8, 21, 45, 0.98));
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        h1 { margin-top: 0; }

        .card {
            margin-top: 14px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #071327;
            font-size: 14px;
            line-height: 1.6;
        }

        .muted { color: var(--muted); }

        .warning {
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px solid rgba(92, 232, 255, 0.5);
            background: rgba(92, 232, 255, 0.12);
            color: #bff7ff;
            border-radius: 8px;
        }

        a {
            display: inline-block;
            margin-top: 18px;
            text-decoration: none;
            color: #032137;
            font-weight: 800;
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            border-radius: 10px;
            padding: 10px 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hasil Tes Kepribadian</h1>
        <p class="muted">Terima kasih, {{ $test->nama }}.</p>
        <p><a href="/handbook?type=DISC" style="text-decoration:none;color:#032137;background:linear-gradient(180deg,var(--accent),var(--accent-2));padding:8px 12px;border-radius:8px;font-weight:700;display:inline-block;">Lihat Panduan Tes</a></p>

        @if (session('warning'))
            <div class="warning">{{ session('warning') }}</div>
        @endif

        <div class="card">
            <div><strong>Status Jawaban:</strong> {{ $answered }} / {{ $totalQuestions }} nomor terisi.</div>
            <div><strong>Institusi:</strong> {{ $test->institusi_perusahaan }}</div>
            <div><strong>Departemen/Divisi:</strong> {{ $test->departemen_divisi }}</div>
            <div><strong>Tanggal Tes:</strong> {{ $test->tanggal_tes?->format('d-m-Y') }}</div>
        </div>

        <div class="card">
            <div><strong>Skor D:</strong> {{ $result->d_score }}</div>
            <div><strong>Skor I:</strong> {{ $result->i_score }}</div>
            <div><strong>Skor S:</strong> {{ $result->s_score }}</div>
            <div><strong>Skor C:</strong> {{ $result->c_score }}</div>
            <div><strong>Tipe Dominan:</strong> {{ $result->dominant_type ?: '-' }}</div>
        </div>

        <div class="card">
            <strong>Rekomendasi Posisi (Deterministic Matching)</strong>
            @if ($recommendations->isEmpty())
                <div class="muted" style="margin-top:8px;">Belum ada benchmark posisi aktif untuk client ini.</div>
            @else
                <ol style="margin-top:8px; padding-left:18px;">
                    @foreach ($recommendations->take(5) as $rec)
                        @php
                            $clientNames = $hasClientPosition ? $rec->position->clients->pluck('name')->implode(', ') : '';
                            $fallbackClient = $rec->position->client->name ?? '-';
                            $displayClient = $clientNames !== '' ? $clientNames : $fallbackClient;
                        @endphp
                        <li>
                            {{ $rec->position->title }}
                            ({{ $displayClient }}) - Skor: {{ number_format($rec->match_score, 2) }}
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

        @if (!empty($narrative))
            <div class="card">
                <strong>Narasi AI (Opsional)</strong>
                <p style="margin:8px 0 0;" class="muted">{{ $narrative }}</p>
            </div>
        @endif

        <a href="/">Mulai Tes Baru</a>
    </div>
</body>
</html>
