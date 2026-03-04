@extends('layouts.tw')

@section('title', 'Builder - ' . $test->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Builder: {{ $test->name }} <span class="text-slate-400">({{ $test->code }})</span></h1>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="/admin/sessions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Kode Sesi Tes</a>
                <a href="/admin/positions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Posisi & Kombinasi Tes</a>
                <a href="/admin/custom-tests" class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-100 text-brand-700 border border-brand-200">Test Builder</a>
                <a href="/admin/analytics" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Analytics & Export</a>
            </div>
        </div>

        <a href="/admin/custom-tests" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Kembali ke daftar</a>
    </header>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm space-y-1">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-xl font-bold text-slate-900">Info Test</h2>
        <p class="mt-2 text-slate-600">{{ $test->description ?: 'Tidak ada deskripsi.' }}</p>
        <div class="mt-3 text-sm text-slate-700">Durasi: {{ $test->time_limit_minutes ? $test->time_limit_minutes . ' menit' : 'Tidak dibatasi' }}</div>
        @if($test->instructions)
            <div class="mt-2 text-sm text-slate-600">Instruksi: {{ $test->instructions }}</div>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7 space-y-4">
        <h2 class="text-xl font-bold text-slate-900">1) Dimensi Skoring</h2>

        <div class="flex flex-wrap gap-2">
            @forelse($test->dimensions as $dimension)
                <span class="inline-flex rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">{{ $dimension->code }} - {{ $dimension->name }} (w{{ $dimension->weight }})</span>
            @empty
                <span class="text-sm text-slate-500">Belum ada dimensi. Tambahkan dulu sebelum membuat logic skor jawaban.</span>
            @endforelse
        </div>

        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/dimensions" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Dimensi</label>
                <input name="code" placeholder="LEADERSHIP" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Dimensi</label>
                <input name="name" placeholder="Leadership" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Bobot</label>
                <input type="number" min="1" max="10" name="weight" value="1" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Urutan</label>
                <input type="number" min="1" max="999" name="sort_order" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div class="md:col-span-2 xl:col-span-4">
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Simpan Dimensi</button>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7 space-y-4">
        <h2 class="text-xl font-bold text-slate-900">2) Pertanyaan & Jawaban + Logic Skor</h2>

        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/questions" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Pertanyaan</label>
                <textarea name="question_text" rows="3" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Pertanyaan</label>
                <select name="question_type" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                    <option value="single_choice">Pilihan Ganda</option>
                    <option value="essay">Essay</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Urutan Soal</label>
                <input type="number" min="1" max="9999" name="sort_order" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="is_required" value="1" checked class="rounded border-slate-300 text-brand-500 focus:ring-brand-400">
                    Wajib dijawab
                </label>
            </div>
            <div class="md:col-span-2">
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Tambah Pertanyaan</button>
            </div>
        </form>

        @foreach($test->questions as $question)
            <article class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-3">
                <div class="font-semibold text-slate-900">Q{{ $question->sort_order }}. {{ $question->question_text }}</div>
                <div class="text-sm text-slate-500">Tipe: {{ $question->question_type }} | Required: {{ $question->is_required ? 'Ya' : 'Tidak' }}</div>

                @if($question->question_type === 'essay')
                    <div class="rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                        Soal tipe essay: responden akan mengisi jawaban teks bebas dan ditinjau reviewer.
                    </div>
                @else
                    <div>
                        <div class="text-sm font-semibold text-slate-700">Opsi yang sudah ada</div>
                        <ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1">
                            @forelse($question->options as $option)
                                <li>{{ $option->option_text }} <span class="text-slate-500">({{ json_encode($option->scores_json) }})</span></li>
                            @empty
                                <li class="text-slate-500">Belum ada opsi.</li>
                            @endforelse
                        </ul>
                    </div>

                    <form method="POST" action="/admin/custom-tests/{{ $test->id }}/questions/{{ $question->id }}/options" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Teks Opsi Jawaban</label>
                            <input name="option_text" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Urutan Opsi</label>
                            <input type="number" min="1" max="9999" name="sort_order" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Logic Skor per Dimensi</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($test->dimensions as $dimension)
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ $dimension->code }}</label>
                                        <input type="number" name="score_{{ strtolower($dimension->code) }}" value="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-400 focus:ring-brand-400">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <button class="px-4 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white font-semibold">Tambah Opsi + Logic Skor</button>
                        </div>
                    </form>
                @endif
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7 space-y-4">
        <h2 class="text-xl font-bold text-slate-900">3) Rule Rekomendasi Posisi untuk Test Ini</h2>

        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/position-rules" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Pilih Posisi</label>
                <select name="position_id" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                    <option value="">- pilih posisi -</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Target Skor per Dimensi</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($test->dimensions as $dimension)
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">{{ $dimension->code }}</label>
                            <input type="number" name="target_{{ strtolower($dimension->code) }}" value="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-400 focus:ring-brand-400">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Rule</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400"></textarea>
            </div>
            <div class="md:col-span-2">
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Simpan Rule Posisi</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">Posisi</th>
                        <th class="py-3 pr-4">Target Skor</th>
                        <th class="py-3 pr-4">Catatan</th>
                        <th class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($test->positionProfiles as $profile)
                        @php
                            $clientNames = $profile->position->clients->pluck('name')->implode(', ');
                            $fallbackClient = $profile->position->client->name ?? '-';
                            $displayClient = $clientNames !== '' ? $clientNames : $fallbackClient;
                        @endphp
                        <tr class="border-b border-slate-100 align-top">
                            <td class="py-3 pr-4">{{ $profile->position->title }} <span class="text-slate-500">({{ $displayClient }})</span></td>
                            <td class="py-3 pr-4">{{ json_encode($profile->target_scores_json) }}</td>
                            <td class="py-3 pr-4">{{ $profile->notes ?: '-' }}</td>
                            <td class="py-3">{{ $profile->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-5 text-slate-500">Belum ada rule posisi untuk test ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
