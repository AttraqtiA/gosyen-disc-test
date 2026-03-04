@extends('layouts.tw')

@section('title', 'Admin - Kode Sesi Tes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Admin Panel</h1>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="/admin/sessions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-100 text-brand-700 border border-brand-200">Kode Sesi Tes</a>
                <a href="/admin/positions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Posisi & Kombinasi Tes</a>
                <a href="/admin/custom-tests" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Test Builder</a>
                <a href="/handbook?type=DISC" target="_blank" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Panduan Tes</a>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                data-theme-toggle
                class="inline-flex items-center justify-center h-11 w-11 rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                aria-label="Aktifkan dark mode"
                title="Aktifkan dark mode"
            >
                <svg data-theme-icon="moon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8 8 0 1010.586 10.586z" />
                </svg>
                <svg data-theme-icon="sun" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 10-1.414 1.414zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 6a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 14.536a1 1 0 10-1.414 1.414l.707-.707a1 1 0 001.414-1.414l-.707.707zM4 11a1 1 0 100-2H3a1 1 0 100 2h1zm1.757-6.364a1 1 0 00-1.414-1.414l-.707.707A1 1 0 105.05 5.343l.707-.707zM14.95 5.343a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707z" clip-rule="evenodd" />
                </svg>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Logout</button>
            </form>
        </div>
    </header>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <h2 class="text-2xl font-bold text-slate-900 mb-5">Buat Kode Akses Sesi</h2>

        <form method="POST" action="/admin/sessions" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Sesi</label>
                <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode (custom)</label>
                <input name="code" value="{{ old('code') }}" placeholder="DISC-MEI26" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Tes</label>
                <select name="test_type" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                    <option value="DISC">DISC</option>
                    <option value="MBTI">MBTI</option>
                    <option value="OCEAN">OCEAN (Big 5)</option>
                    <option value="OTHER">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Client (pilih)</label>
                <select name="client_id" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                    <option value="">-</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Atau Nama Client Baru</label>
                <input name="client_name" value="{{ old('client_name') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kedaluwarsa (opsional)</label>
                <input name="expires_at" type="datetime-local" value="{{ old('expires_at') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div class="md:col-span-2">
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Buat Kode Sesi</button>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-2xl font-bold text-slate-900 mb-5">Daftar Sesi</h2>

        <div class="hidden xl:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">Kode</th>
                        <th class="py-3 pr-4">Nama Sesi</th>
                        <th class="py-3 pr-4">Tipe</th>
                        <th class="py-3 pr-4">Client</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="py-4 pr-4 font-semibold">{{ $session->code }}</td>
                            <td class="py-4 pr-4">{{ $session->name }}</td>
                            <td class="py-4 pr-4">{{ $session->test_type }}</td>
                            <td class="py-4 pr-4">{{ $session->client->name ?? '-' }}</td>
                            <td class="py-4 pr-4">
                                <span class="font-semibold {{ $session->is_active ? 'text-emerald-600' : 'text-rose-600' }}">{{ $session->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="py-4">
                                <div class="space-y-2">
                                    <form method="POST" action="/admin/sessions/{{ $session->id }}/toggle">
                                        @csrf @method('PATCH')
                                        <button class="px-3 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white font-semibold">{{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>

                                    <details>
                                        <summary class="cursor-pointer text-brand-700 font-semibold">Edit</summary>
                                        <form method="POST" action="/admin/sessions/{{ $session->id }}" class="mt-2 grid grid-cols-1 gap-2 max-w-sm">
                                            @csrf @method('PATCH')
                                            <input name="name" value="{{ $session->name }}" required class="rounded-lg border-slate-300">
                                            <input name="code" value="{{ $session->code }}" required class="rounded-lg border-slate-300">
                                            <select name="test_type" class="rounded-lg border-slate-300">
                                                <option value="DISC" @selected($session->test_type==='DISC')>DISC</option>
                                                <option value="MBTI" @selected($session->test_type==='MBTI')>MBTI</option>
                                                <option value="OCEAN" @selected($session->test_type==='OCEAN')>OCEAN (Big 5)</option>
                                                <option value="OTHER" @selected($session->test_type==='OTHER')>Other</option>
                                            </select>
                                            <select name="client_id" class="rounded-lg border-slate-300">
                                                <option value="">-</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" @selected($session->client_id === $client->id)>{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                            <input name="client_name" placeholder="Atau nama client baru" class="rounded-lg border-slate-300">
                                            <input name="expires_at" type="datetime-local" value="{{ $session->expires_at?->format('Y-m-d\\TH:i') }}" class="rounded-lg border-slate-300">
                                            <button class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold">Simpan Edit</button>
                                        </form>
                                    </details>

                                    <form method="POST" action="/admin/sessions/{{ $session->id }}" onsubmit="return confirm('Hapus sesi ini?');">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-2 rounded-lg border border-rose-300 text-rose-700 font-semibold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-slate-500">Belum ada sesi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-3 xl:hidden">
            @forelse($sessions as $session)
                <article class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold text-slate-900">{{ $session->code }}</h3>
                        <span class="text-sm font-semibold {{ $session->is_active ? 'text-emerald-600' : 'text-rose-600' }}">{{ $session->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <div class="text-sm text-slate-700">{{ $session->name }}</div>
                    <div class="text-sm text-slate-500">{{ $session->test_type }} • {{ $session->client->name ?? '-' }}</div>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <form method="POST" action="/admin/sessions/{{ $session->id }}/toggle">@csrf @method('PATCH')<button class="px-3 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold">{{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                        <form method="POST" action="/admin/sessions/{{ $session->id }}" onsubmit="return confirm('Hapus sesi ini?');">@csrf @method('DELETE')<button class="px-3 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-semibold">Hapus</button></form>
                    </div>
                </article>
            @empty
                <div class="text-slate-500">Belum ada sesi.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $sessions->links() }}</div>
    </section>
</div>
@endsection
