<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBTI - Soal {{ $number }}</title>
    <style>
        :root { --bg:#030b1d; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --accent:#5ce8ff; --accent-2:#35d0f5; }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; font-family:Arial,sans-serif; color:var(--text); background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg); padding:24px 16px; }
        .container { max-width:980px; margin:0 auto; background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98)); border:1px solid var(--line); border-radius:16px; padding:20px; box-shadow:0 20px 50px rgba(0,0,0,.35); }
        .head { display:flex; justify-content:space-between; align-items:center; gap:10px; }
        .head h2 { margin:0; }
        .badge { padding:8px 12px; border-radius:999px; background:rgba(92,232,255,.16); border:1px solid rgba(92,232,255,.35); color:#bff7ff; font-weight:700; font-size:13px; }
        .progress { margin-top:12px; background:#071327; border:1px solid var(--line); height:9px; border-radius:999px; overflow:hidden; }
        .progress > div { background:linear-gradient(90deg,var(--accent),var(--accent-2)); height:100%; }
        .hint { margin-top:14px; color:var(--muted); font-size:14px; }
        .options { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:16px; }
        .option { border:1px solid var(--line); border-radius:12px; padding:14px; background:#071327; cursor:pointer; }
        .option input { margin-right:8px; accent-color:var(--accent); }
        .trait { display:inline-block; margin-bottom:8px; padding:4px 10px; border-radius:999px; background:rgba(92,232,255,.12); border:1px solid rgba(92,232,255,.35); color:#bff7ff; font-size:12px; font-weight:700; }
        .text { line-height:1.5; }
        .actions { margin-top:16px; display:flex; justify-content:space-between; align-items:center; gap:12px; }
        .error { color:#ffc5de; font-size:13px; margin-top:8px; }
        button { border:0; border-radius:10px; background:linear-gradient(180deg,var(--accent),var(--accent-2)); color:#032137; padding:11px 16px; font-weight:800; cursor:pointer; white-space:nowrap; }
        @media (max-width:768px){ .options { grid-template-columns:1fr; } .actions,.head { flex-direction:column; align-items:flex-start; } }
    </style>
</head>
<body>
<div class="container">
    <div class="head">
        <h2>MBTI - Soal {{ $number }} dari {{ $totalQuestions }}</h2>
        <div id="timer" class="badge">Sisa waktu: --:--</div>
    </div>
    <div class="progress"><div style="width: {{ (int)(($number / max($totalQuestions, 1)) * 100) }}%"></div></div>

    @if (session('warning'))
        <p class="hint" style="color:#bff7ff;">{{ session('warning') }}</p>
    @endif

    <p class="hint">Pilih pernyataan yang paling menggambarkan diri Anda secara umum.</p>

    <form id="answer-form" method="POST" action="/mbti/test/{{ $test->id }}/answer">
        @csrf
        <input type="hidden" name="mbti_question_id" value="{{ $question->id }}">
        <input type="hidden" name="question_number" value="{{ $number }}">

        <div class="options">
            <label class="option">
                <input type="radio" name="selected_trait" value="{{ $question->trait_a }}" @checked(old('selected_trait', optional($existingAnswer)->selected_trait) === $question->trait_a) required>
                <div class="trait">{{ $question->trait_a }}</div>
                <div class="text">{{ $question->text_a }}</div>
            </label>
            <label class="option">
                <input type="radio" name="selected_trait" value="{{ $question->trait_b }}" @checked(old('selected_trait', optional($existingAnswer)->selected_trait) === $question->trait_b) required>
                <div class="trait">{{ $question->trait_b }}</div>
                <div class="text">{{ $question->text_b }}</div>
            </label>
        </div>

        @error('selected_trait')<div class="error">{{ $message }}</div>@enderror

        <div class="actions">
            <span class="hint">Jawab secara jujur sesuai kebiasaan Anda, bukan sesuai ekspektasi orang lain.</span>
            <button type="submit">{{ $number === $totalQuestions ? 'Selesai Tes' : 'Lanjut' }}</button>
        </div>
    </form>
</div>

<script>
    (function () {
        const timerEl = document.getElementById('timer');
        let remaining = {{ (int) $remainingSeconds }};

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
                window.location.href = '/mbti/test/{{ $test->id }}/result';
                return;
            }
            renderTimer();
        }, 1000);
    })();
</script>
</body>
</html>
