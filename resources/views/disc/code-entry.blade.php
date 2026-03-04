@extends('layouts.tw')

@section('title', 'Tes Kepribadian - Akses Kode')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10 sm:px-6">
    <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Tes Kepribadian</h1>
        <p class="mt-2 text-slate-600">Masukkan kode sesi dari admin untuk memulai tes (DISC, MBTI, atau OCEAN).</p>

        @error('code')
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</div>
        @enderror

        <form method="POST" action="/access" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Sesi</label>
                <input id="code" name="code" value="{{ old('code') }}" required autofocus class="w-full rounded-xl border-slate-300 px-4 py-3 uppercase focus:border-brand-400 focus:ring-brand-400">
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-1">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-white font-bold hover:bg-brand-600">Masuk Tes</button>
                <a href="/login" class="text-brand-700 hover:text-brand-800 font-semibold">Login Admin</a>
            </div>
        </form>
    </div>
</div>
@endsection
