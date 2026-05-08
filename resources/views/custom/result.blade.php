@extends('layouts.tw')

@section('title', 'Hasil Custom Test')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-10 sm:px-6">
    <div class="mx-auto max-w-5xl space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Ringkasan Hasil</h1>
            <p class="mt-2 text-slate-600">{{ $submission->nama }} - {{ $submission->session?->name }}</p>
            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Total Bagian</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $packetSummary->count() }}</div>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Total Raw Score</div>
                    <div class="mt-1 text-2xl font-bold text-brand-700">{{ $totalRaw }}</div>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Rata-rata Raw Score</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($averageRaw, 2) }}</div>
                </article>
            </div>
            @if(!$packetComplete)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Packet ini belum lengkap. Hasil akhir akan utuh setelah semua bagian selesai.
                </div>
            @else
                <div class="mt-4 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                    Untuk IST, tampilan ini paling aman dipakai sebagai rekap raw score per subtes dan rata-rata packet. Konversi ke weighted score sebaiknya memakai tabel norma terpisah supaya hasil tidak salah.
                </div>
            @endif
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-bold text-slate-900">Per Bagian</h2>
            <div class="mt-5 space-y-3">
                @foreach($packetSummary as $item)
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-lg font-bold text-slate-900">{{ $item['submission']->customTest->name }}</div>
                                <div class="text-sm text-slate-500">{{ $item['submission']->customTest->code }} • {{ $item['answered'] }}/{{ $item['total_questions'] }} terjawab</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-slate-500">Raw Score</div>
                                <div class="text-2xl font-bold text-brand-700">{{ $item['raw_score'] }}</div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
