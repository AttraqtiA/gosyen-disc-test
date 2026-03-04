@extends('layouts.tw')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10 sm:px-6">
    <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Login Admin</h1>
        <p class="mt-2 text-slate-600">Masuk untuk mengelola sesi tes, benchmark posisi, dan hasil responden.</p>

        @error('email')
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</div>
        @enderror

        <form method="POST" action="/login" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400">
            </div>

            <div class="pt-1 flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-white font-bold hover:bg-brand-600">Login</button>
                <a href="/" class="text-brand-700 hover:text-brand-800 font-semibold">Kembali ke akses kode</a>
            </div>
        </form>
    </div>
</div>
@endsection
