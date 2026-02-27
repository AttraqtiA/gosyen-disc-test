<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Kepribadian - Soal {{ $number }}</title>
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
            padding: 24px 16px;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
            background: linear-gradient(180deg, rgba(13, 32, 66, 0.96), rgba(8, 21, 45, 0.98));
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .head { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .head h2 { margin: 0; }

        .badge {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(92, 232, 255, 0.16);
            border: 1px solid rgba(92, 232, 255, 0.35);
            color: #bff7ff;
            font-weight: 700;
            font-size: 13px;
        }

        .progress {
            margin-top: 12px;
            background: #071327;
            border: 1px solid var(--line);
            height: 9px;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress > div { background: linear-gradient(90deg, var(--accent), var(--accent-2)); height: 100%; }

        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid var(--line); padding: 10px; font-size: 14px; }
        th { background: #071327; text-align: left; color: var(--muted); }
        .center { text-align: center; }

        input[type="radio"] { accent-color: var(--accent); }

        .actions { margin-top: 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }

        .hint { font-size: 13px; color: var(--muted); }
        .error { color: #ffc5de; font-size: 13px; margin-top: 6px; }

        button {
            border: 0;
            border-radius: 10px;
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            color: #032137;
            padding: 11px 16px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .actions { flex-direction: column; align-items: flex-start; }
            .head { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="head">
        <h2>Tes Kepribadian - Soal {{ $number }} dari 24</h2>
        <div id="timer" class="badge">Sisa waktu: --:--</div>
    </div>
    <div class="progress"><div style="width: {{ (int)(($number / 24) * 100) }}%"></div></div>

    @if (session('warning'))
        <p class="hint" style="color:#bff7ff; margin-top: 10px;">{{ session('warning') }}</p>
    @endif

    <p class="hint" style="margin-top: 14px;">
        Pilih satu pernyataan yang <strong>Paling menggambarkan</strong> diri Anda (P),
        dan satu yang <strong>Paling Tidak menggambarkan</strong> diri Anda (K).
    </p>

    <form id="answer-form" method="POST" action="/test/{{ $test->id }}/answer">
        @csrf
        <input type="hidden" name="disc_question_id" value="{{ $question->id }}">
        <input type="hidden" name="question_number" value="{{ $number }}">

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:90px;">P</th>
                    <th class="center" style="width:90px;">K</th>
                    <th>Pernyataan</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($question->statements as $statement)
                <tr>
                    <td class="center">
                        <input
                            type="radio"
                            name="p"
                            value="{{ $statement->id }}"
                            @checked((int) old('p', optional($existingAnswer)->p_statement_id) === $statement->id)
                            required
                        >
                    </td>
                    <td class="center">
                        <input
                            type="radio"
                            name="k"
                            value="{{ $statement->id }}"
                            @checked((int) old('k', optional($existingAnswer)->k_statement_id) === $statement->id)
                            required
                        >
                    </td>
                    <td>{{ $statement->text }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @error('p')<div class="error">{{ $message }}</div>@enderror
        @error('k')<div class="error">{{ $message }}</div>@enderror

        <div class="actions">
            <span class="hint">Mohon isi dengan jujur agar hasil tes mencerminkan karakter Anda yang sebenarnya.</span>
            <button type="submit">{{ $number === 24 ? 'Selesai Tes' : 'Lanjut' }}</button>
        </div>
    </form>
</div>

<script>
    (function () {
        const form = document.getElementById('answer-form');
        const pInputs = document.querySelectorAll('input[name="p"]');
        const kInputs = document.querySelectorAll('input[name="k"]');
        const timerEl = document.getElementById('timer');
        let remaining = {{ (int) $remainingSeconds }};

        form.addEventListener('submit', function (e) {
            const p = document.querySelector('input[name="p"]:checked');
            const k = document.querySelector('input[name="k"]:checked');
            if (p && k && p.value === k.value) {
                e.preventDefault();
                alert('Pilihan P dan K tidak boleh sama.');
            }
        });

        function pad(value) { return String(value).padStart(2, '0'); }

        function renderTimer() {
            const min = Math.floor(remaining / 60);
            const sec = remaining % 60;
            timerEl.textContent = 'Sisa waktu: ' + pad(min) + ':' + pad(sec);
        }

        renderTimer();
        const interval = setInterval(function () {
            remaining -= 1;
            if (remaining <= 0) {
                clearInterval(interval);
                window.location.href = '/test/{{ $test->id }}/result';
                return;
            }
            renderTimer();
        }, 1000);

        pInputs.forEach((input) => {
            input.addEventListener('change', function () {
                const k = document.querySelector('input[name="k"]:checked');
                if (k && k.value === this.value) {
                    k.checked = false;
                }
            });
        });

        kInputs.forEach((input) => {
            input.addEventListener('change', function () {
                const p = document.querySelector('input[name="p"]:checked');
                if (p && p.value === this.value) {
                    p.checked = false;
                }
            });
        });
    })();
</script>
</body>
</html>
