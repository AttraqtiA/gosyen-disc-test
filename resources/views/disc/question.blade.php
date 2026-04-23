@extends('layouts.tw')

@section('title', 'DISC - Soal ' . $number)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Tes Kepribadian - Soal {{ $number }} dari 24</h1>
            <div id="timer" class="inline-flex items-center rounded-full bg-brand-50 border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-700">Sisa waktu: --:--</div>
        </div>

        <div class="mt-4 h-2 w-full rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-brand-500" style="width: {{ (int)(($number / 24) * 100) }}%"></div></div>

        @if (session('warning'))
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
        @endif

        <p class="mt-4 text-slate-600">Pilih satu pernyataan yang <strong>Paling menggambarkan</strong> diri Anda (P), dan satu yang <strong>Paling Tidak menggambarkan</strong> diri Anda (K).</p>

        <form id="answer-form" method="POST" action="/test/{{ $test->id }}/answer" class="mt-5 space-y-4">
            @csrf
            <input type="hidden" name="disc_question_id" value="{{ $question->id }}">
            <input type="hidden" name="question_number" value="{{ $number }}">
            <input type="hidden" name="target_number" id="target-number" value="{{ $number }}">

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-700">Navigasi Soal</p>
                    <p class="text-xs text-slate-500">{{ count($questionNumbersById) }} / 24 terisi</p>
                </div>
                <div class="mt-3 grid grid-cols-6 sm:grid-cols-8 md:grid-cols-12 gap-2">
                    @for ($i = 1; $i <= 24; $i++)
                        @php
                            $isCurrent = $i === $number;
                            $isAnswered = in_array($i, $questionNumbersById, true);
                        @endphp
                        <button
                            type="submit"
                            name="action"
                            value="goto"
                            data-target-number="{{ $i }}"
                            formnovalidate
                            class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold {{ $isCurrent ? 'border-brand-500 bg-brand-500 text-white' : ($isAnswered ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-100') }}"
                        >
                            {{ $i }}
                        </button>
                    @endfor
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600">
                            <th class="px-4 py-3 text-center w-20">P</th>
                            <th class="px-4 py-3 text-center w-20">K</th>
                            <th class="px-4 py-3 text-left">Pernyataan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($question->statements as $statement)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 text-center"><input type="radio" name="p" value="{{ $statement->id }}" @checked((int) old('p', optional($existingAnswer)->p_statement_id) === $statement->id)></td>
                            <td class="px-4 py-3 text-center"><input type="radio" name="k" value="{{ $statement->id }}" @checked((int) old('k', optional($existingAnswer)->k_statement_id) === $statement->id)></td>
                            <td class="px-4 py-3 text-slate-800">{{ $statement->text }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @error('p')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror
            @error('k')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror

            <div class="flex flex-col gap-3 pt-1">
                <p class="text-sm text-slate-500">Mohon isi dengan jujur agar hasil tes mencerminkan karakter Anda yang sebenarnya.</p>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex gap-2">
                        <button type="submit" name="action" value="prev" formnovalidate class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50" @disabled($number === 1)>Sebelumnya</button>
                        <button type="submit" name="action" value="next" class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-white font-bold hover:bg-brand-600">{{ $number === 24 ? 'Simpan & Tetap di Akhir' : 'Simpan & Lanjut' }}</button>
                    </div>
                    <button type="submit" name="action" value="finish" class="inline-flex items-center justify-center rounded-xl border border-brand-300 bg-brand-50 px-5 py-3 font-bold text-brand-700 hover:bg-brand-100">Selesaikan Tes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const form = document.getElementById('answer-form');
        const pInputs = document.querySelectorAll('input[name="p"]');
        const kInputs = document.querySelectorAll('input[name="k"]');
        const timerEl = document.getElementById('timer');
        const targetNumberInput = document.getElementById('target-number');
        const gotoButtons = document.querySelectorAll('[data-target-number]');
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

        gotoButtons.forEach((button) => {
            button.addEventListener('click', function () {
                targetNumberInput.value = this.getAttribute('data-target-number');
            });
        });
    })();
</script>
@endsection
