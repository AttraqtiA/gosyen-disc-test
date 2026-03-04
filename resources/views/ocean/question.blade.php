@extends('layouts.tw')

@section('title', 'OCEAN - Soal ' . $number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">OCEAN - Soal {{ $number }} dari {{ $totalQuestions }}</h1>
            <div id="timer" class="inline-flex items-center rounded-full bg-brand-50 border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-700">Sisa waktu: --:--</div>
        </div>

        <div class="mt-4 h-2 w-full rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-brand-500" style="width: {{ (int)(($number / max($totalQuestions, 1)) * 100) }}%"></div></div>

        @if (session('warning'))
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
        @endif

        <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pernyataan</p>
            <p class="mt-1 text-lg font-semibold text-slate-900">{{ $question->statement }}</p>
            <p class="mt-2 text-sm text-slate-500">Pilih tingkat kesesuaian pernyataan ini dengan diri Anda.</p>
        </div>

        <form method="POST" action="/ocean/test/{{ $test->id }}/answer" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="ocean_question_id" value="{{ $question->id }}">
            <input type="hidden" name="question_number" value="{{ $number }}">

            @php
                $labels = [
                    1 => 'Sangat Tidak Sesuai',
                    2 => 'Tidak Sesuai',
                    3 => 'Netral',
                    4 => 'Sesuai',
                    5 => 'Sangat Sesuai',
                ];
                $selected = (int) old('score', optional($existingAnswer)->score);
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                @foreach($labels as $value => $label)
                    <label class="rounded-xl border border-slate-200 bg-slate-50 p-3 cursor-pointer hover:border-brand-300">
                        <div class="flex items-start gap-2">
                            <input type="radio" name="score" value="{{ $value }}" @checked($selected === $value) required class="mt-1">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $value }}</div>
                                <div class="text-xs text-slate-600">{{ $label }}</div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('score')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                <p class="text-sm text-slate-500">Jawab secara jujur agar profil OCEAN merepresentasikan gaya kerja Anda dengan akurat.</p>
                <button class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-white font-bold hover:bg-brand-600" type="submit">{{ $number === $totalQuestions ? 'Selesai Tes' : 'Lanjut' }}</button>
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
                window.location.href = '/ocean/test/{{ $test->id }}/result';
                return;
            }
            renderTimer();
        }, 1000);
    })();
</script>
@endsection
