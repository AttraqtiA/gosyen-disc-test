@extends('layouts.tw')

@section('title', 'Admin - Review Essay')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Review Essay per Sesi</h1>
            <p class="mt-2 text-slate-600">Pilih sesi aktif, lalu lakukan review jawaban essay pada sesi tersebut.</p>
        </div>

        <a href="/admin" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Kembali ke Admin</a>
    </header>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Sesi Aktif yang Memiliki Essay</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @forelse($sessions as $session)
                <a href="/admin/reviews?session_id={{ $session->id }}&status={{ $status }}" class="rounded-xl border p-4 {{ (int)$selectedSessionId === (int)$session->id ? 'border-brand-300 bg-brand-50' : 'border-slate-200 bg-slate-50 hover:bg-slate-100' }}">
                    <div class="font-bold text-slate-900">{{ $session->code }} • {{ $session->name }}</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $session->test_type }} • {{ $session->client->name ?? '-' }}</div>
                    <div class="mt-2 text-sm">
                        <span class="text-amber-700 font-semibold">Pending: {{ $session->pending_count }}</span>
                        <span class="mx-2 text-slate-300">|</span>
                        <span class="text-emerald-700 font-semibold">Reviewed: {{ $session->reviewed_count }}</span>
                    </div>
                </a>
            @empty
                <div class="text-slate-500">Belum ada sesi aktif dengan jawaban essay.</div>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="/admin/reviews?session_id={{ $selectedSessionId }}&status=pending_review" class="px-4 py-2 rounded-xl text-sm font-semibold border {{ $status === 'pending_review' ? 'bg-brand-100 text-brand-700 border-brand-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">Pending</a>
            <a href="/admin/reviews?session_id={{ $selectedSessionId }}&status=reviewed" class="px-4 py-2 rounded-xl text-sm font-semibold border {{ $status === 'reviewed' ? 'bg-brand-100 text-brand-700 border-brand-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">Reviewed</a>
        </div>

        <div class="space-y-4">
            @forelse($items as $item)
                <article class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-3">
                    <div class="text-sm text-slate-500">
                        {{ $item->submission?->session?->code ?? '-' }} • {{ $item->question?->test?->name ?? '-' }} • {{ $item->submission?->client?->name ?? '-' }}
                    </div>
                    <h3 class="font-bold text-slate-900">{{ $item->question?->question_text }}</h3>
                    <div class="rounded-lg border border-slate-200 bg-white p-3 text-slate-700 whitespace-pre-wrap">{{ $item->answer_text ?: '-' }}</div>

                    <form method="POST" action="/admin/reviews/{{ $item->id }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Score Reviewer (0-100)</label>
                            <input type="number" min="0" max="100" name="reviewer_score" value="{{ old('reviewer_score', $item->reviewer_score) }}" class="w-full rounded-xl border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                            <select name="review_status" class="w-full rounded-xl border-slate-300">
                                <option value="pending_review" @selected($item->review_status === 'pending_review')>Pending</option>
                                <option value="reviewed" @selected($item->review_status === 'reviewed')>Reviewed</option>
                            </select>
                        </div>
                        <div>
                            <button class="px-4 py-2 rounded-xl bg-brand-500 text-white font-semibold hover:bg-brand-600">Simpan Review</button>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Reviewer</label>
                            <textarea name="reviewer_notes" rows="3" class="w-full rounded-xl border-slate-300">{{ old('reviewer_notes', $item->reviewer_notes) }}</textarea>
                        </div>
                    </form>
                </article>
            @empty
                <div class="text-slate-500">Belum ada jawaban essay pada sesi ini.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </section>
</div>
@endsection
