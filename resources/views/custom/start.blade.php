@extends('layouts.tw')

@section('title', 'Custom Test - Mulai')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-10 sm:px-6">
    <div class="mx-auto max-w-4xl space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $session->name }}</h1>
            <p class="mt-2 text-slate-600">Kode sesi {{ $session->code }} terdiri dari {{ $items->count() }} test yang akan dikerjakan berurutan.</p>

            @if($items->isNotEmpty())
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach($items as $index => $test)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">Bagian {{ $index + 1 }}</div>
                            <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $test->name }}</h2>
                            <p class="mt-1 text-sm text-slate-600">{{ $test->description }}</p>
                            <div class="mt-3 text-sm text-slate-500">Durasi: {{ $test->time_limit_minutes ? $test->time_limit_minutes . ' menit' : 'Tidak dibatasi' }}</div>
                            @if($test->instructions)
                                <div class="mt-2 text-sm text-slate-600">{{ $test->instructions }}</div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 space-y-1">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <h2 class="text-2xl font-bold text-slate-900">Data Peserta</h2>
            <form method="POST" action="/custom/start" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                <input type="hidden" name="access_code" value="{{ $session->code }}">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nama</label>
                    <input name="nama" value="{{ old('nama') }}" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nomor HP</label>
                    <input name="nomor_hp" value="{{ old('nomor_hp') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Institusi / Perusahaan</label>
                    <input name="institusi_perusahaan" value="{{ old('institusi_perusahaan') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Departemen / Divisi</label>
                    <input name="departemen_divisi" value="{{ old('departemen_divisi') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Jabatan Saat Ini</label>
                    <input name="jabatan_saat_ini" value="{{ old('jabatan_saat_ini') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                </div>
                <div class="md:col-span-2">
                    <button class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 font-bold text-white hover:bg-brand-600">Mulai Packet Test</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
