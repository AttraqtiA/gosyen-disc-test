@extends('layouts.tw')

@section('title', $submission->customTest->name . ' - Soal ' . $number)

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6">
    <div class="mx-auto max-w-6xl space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="text-sm font-semibold uppercase tracking-[0.22em] text-brand-700">Packet {{ $submission->packet_index }}/{{ $submission->packet_size }}</div>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ $submission->customTest->name }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Soal {{ $number }} dari {{ $totalQuestions }}</p>
                </div>
                @if($remainingSeconds !== PHP_INT_MAX)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                        Sisa waktu: <span data-remaining-seconds="{{ $remainingSeconds }}">{{ gmdate('i:s', $remainingSeconds) }}</span>
                    </div>
                @endif
            </div>
        </header>

        @if (session('warning'))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 space-y-1">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_260px]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="/custom/test/{{ $submission->id }}/answer" class="space-y-6">
                    @csrf
                    <input type="hidden" name="custom_test_question_id" value="{{ $question->id }}">
                    <input type="hidden" name="question_number" value="{{ $number }}">

                    <div>
                        <div class="text-lg font-bold text-slate-900">{{ $question->question_text }}</div>
                        @if($question->image_path)
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar soal {{ $number }}" class="mt-4 max-h-[28rem] rounded-2xl border border-slate-200 bg-slate-50 p-3">
                        @endif
                    </div>

                    @if($question->question_type === 'essay')
                        <textarea name="answer_text" rows="8" class="w-full rounded-2xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">{{ old('answer_text', $existingAnswer?->answer_text) }}</textarea>
                    @else
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex cursor-pointer flex-col gap-3 rounded-2xl border border-slate-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" name="custom_test_option_id" value="{{ $option->id }}" @checked((string) old('custom_test_option_id', $existingAnswer?->custom_test_option_id) === (string) $option->id) class="mt-1 border-slate-300 text-brand-500 focus:ring-brand-400">
                                        <div class="font-medium text-slate-800">{{ $option->option_text }}</div>
                                    </div>
                                    @if($option->image_path)
                                        <img src="{{ asset('storage/' . $option->image_path) }}" alt="Gambar opsi" class="max-h-64 rounded-xl border border-slate-200 bg-slate-50 p-2">
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        <button name="action" value="prev" class="rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50">Sebelumnya</button>
                        @if($number < $totalQuestions)
                            <button name="action" value="next" class="rounded-xl bg-brand-500 px-5 py-3 font-bold text-white hover:bg-brand-600">Simpan & Lanjut</button>
                        @else
                            <button name="action" value="finish" class="rounded-xl bg-emerald-600 px-5 py-3 font-bold text-white hover:bg-emerald-700">Selesaikan Bagian Ini</button>
                        @endif
                    </div>
                </form>
            </section>

            <aside class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Navigasi</div>
                <div class="mt-4 grid grid-cols-5 gap-2">
                    @for($i = 1; $i <= $totalQuestions; $i++)
                        @php
                            $isAnswered = $answeredNumbers->contains($i);
                            $isCurrent = $i === $number;
                        @endphp
                        <a href="/custom/test/{{ $submission->id }}/question/{{ $i }}" class="flex h-11 items-center justify-center rounded-xl border text-sm font-bold {{ $isCurrent ? 'border-brand-500 bg-brand-500 text-white' : ($isAnswered ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-600') }}">{{ $i }}</a>
                    @endfor
                </div>

                <div class="mt-6 text-sm text-slate-600">
                    @foreach($packetItems as $index => $item)
                        <div class="{{ $index + 1 === $submission->packet_index ? 'font-bold text-slate-900' : '' }}">{{ $index + 1 }}. {{ $item->name }}</div>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const el = document.querySelector('[data-remaining-seconds]');
        if (!el) return;

        let remaining = Number(el.getAttribute('data-remaining-seconds'));
        const tick = () => {
            const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');
            el.textContent = `${minutes}:${seconds}`;
            if (remaining <= 0) {
                window.location.reload();
                return;
            }
            remaining -= 1;
        };

        tick();
        setInterval(tick, 1000);
    })();
</script>
@endsection
