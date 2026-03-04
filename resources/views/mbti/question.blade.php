@extends('layouts.tw')

@section('title', 'MBTI - Soal ' . $number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">MBTI - Soal {{ $number }} dari {{ $totalQuestions }}</h1>
            <div id="timer" class="inline-flex items-center rounded-full bg-brand-50 border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-700">Sisa waktu: --:--</div>
        </div>

        <div class="mt-4 h-2 w-full rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-brand-500" style="width: {{ (int)(($number / max($totalQuestions, 1)) * 100) }}%"></div></div>

        @if (session('warning'))
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
        @endif

        <p class="mt-4 text-slate-600">Pilih pernyataan yang paling menggambarkan diri Anda secara umum.</p>

        <form id="answer-form" method="POST" action="/mbti/test/{{ $test->id }}/answer" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="mbti_question_id" value="{{ $question->id }}">
            <input type="hidden" name="question_number" value="{{ $number }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="rounded-xl border border-slate-200 bg-slate-50 p-4 cursor-pointer hover:border-brand-300">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="selected_trait" value="{{ $question->trait_a }}" @checked(old('selected_trait', optional($existingAnswer)->selected_trait) === $question->trait_a) required class="mt-1">
                        <div>
                            <div class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-2 py-0.5 text-xs font-semibold text-brand-700">{{ $question->trait_a }}</div>
                            <div class="mt-2 text-slate-800">{{ $question->text_a }}</div>
                        </div>
                    </div>
                </label>
                <label class="rounded-xl border border-slate-200 bg-slate-50 p-4 cursor-pointer hover:border-brand-300">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="selected_trait" value="{{ $question->trait_b }}" @checked(old('selected_trait', optional($existingAnswer)->selected_trait) === $question->trait_b) required class="mt-1">
                        <div>
                            <div class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-2 py-0.5 text-xs font-semibold text-brand-700">{{ $question->trait_b }}</div>
                            <div class="mt-2 text-slate-800">{{ $question->text_b }}</div>
                        </div>
                    </div>
                </label>
            </div>

            @error('selected_trait')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                <p class="text-sm text-slate-500">Jawab secara jujur sesuai kebiasaan Anda, bukan sesuai ekspektasi orang lain.</p>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-white font-bold hover:bg-brand-600">{{ $number === $totalQuestions ? 'Selesai Tes' : 'Lanjut' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
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
@endsection
