@extends('layouts.tw')

@section('title', 'Hasil Tes Kepribadian DISC')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-4">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Hasil Tes Kepribadian DISC</h1>
        <p class="mt-2 text-slate-600">Terima kasih, {{ $test->nama }}.</p>
        <a href="/handbook?type=DISC" class="mt-4 inline-flex items-center justify-center rounded-xl bg-brand-500 px-5 py-2.5 text-white font-bold hover:bg-brand-600">Lihat Panduan Tes</a>

        @if (session('warning'))
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('warning') }}</div>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div><strong>Status Jawaban:</strong> {{ $answered }} / {{ $totalQuestions }} nomor terisi.</div>
        <div><strong>Tanggal Tes:</strong> {{ $test->tanggal_tes?->format('d-m-Y') }}</div>
        <div><strong>Institusi:</strong> {{ $test->institusi_perusahaan }}</div>
        <div><strong>Departemen/Divisi:</strong> {{ $test->departemen_divisi }}</div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <h2 class="text-xl font-bold text-slate-900 mb-3">Skor DISC</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><strong>D:</strong> {{ $result->d_score }}</div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><strong>I:</strong> {{ $result->i_score }}</div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><strong>S:</strong> {{ $result->s_score }}</div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3"><strong>C:</strong> {{ $result->c_score }}</div>
            <div class="rounded-xl border border-brand-200 bg-brand-50 p-3"><strong>Dominan:</strong> {{ $result->dominant_type ?: '-' }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <h2 class="text-xl font-bold text-slate-900 mb-2">Rekomendasi Posisi (Deterministic Matching)</h2>
        @if ($recommendations->isEmpty())
            <p class="text-sm text-slate-500">Belum ada benchmark posisi aktif untuk client ini.</p>
        @else
            <ol class="list-decimal pl-5 space-y-1 text-sm text-slate-700">
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

    @if (!empty($narrative))
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Narasi AI (Opsional)</h2>
            <p class="text-sm text-slate-600">{{ $narrative }}</p>
        </div>
    @endif

    <a href="/" class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-5 py-2.5 text-white font-bold hover:bg-brand-600">Mulai Tes Baru</a>
</div>
@endsection
