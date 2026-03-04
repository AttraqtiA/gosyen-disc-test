@extends('layouts.tw')

@section('title', 'Admin - Test Builder')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Admin Panel</h1>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="/admin/sessions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Kode Sesi Tes</a>
                <a href="/admin/positions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Posisi & Kombinasi Tes</a>
                <a href="/admin/custom-tests" class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-100 text-brand-700 border border-brand-200">Test Builder</a>
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
            <form method="POST" action="{{ route('logout') }}">@csrf
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

        <h2 class="text-2xl font-bold text-slate-900 mb-5">Buat Test Baru (Custom)</h2>

        <form method="POST" action="/admin/custom-tests" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Test</label>
                <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Test (unik)</label>
                <input name="code" value="{{ old('code') }}" placeholder="CULTUREFIT" required class="w-full rounded-xl border-slate-300 uppercase focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Durasi (menit, opsional)</label>
                <input type="number" name="time_limit_minutes" min="1" max="240" value="{{ old('time_limit_minutes') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">{{ old('description') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Instruksi Responden</label>
                <textarea name="instructions" rows="3" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">{{ old('instructions') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Buat Test</button>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-2xl font-bold text-slate-900 mb-5">Daftar Custom Test</h2>

        <div class="hidden xl:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">Kode</th>
                        <th class="py-3 pr-4">Nama</th>
                        <th class="py-3 pr-4">Konten</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tests as $test)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="py-4 pr-4 font-semibold">{{ $test->code }}</td>
                        <td class="py-4 pr-4">
                            <div class="font-semibold text-slate-900">{{ $test->name }}</div>
                            @if($test->description)
                                <div class="text-slate-500 mt-1">{{ $test->description }}</div>
                            @endif
                        </td>
                        <td class="py-4 pr-4 text-slate-700">
                            <div>{{ $test->dimensions_count }} dimensi</div>
                            <div>{{ $test->questions_count }} pertanyaan</div>
                            <div>Durasi: {{ $test->time_limit_minutes ? $test->time_limit_minutes . ' menit' : 'Tidak dibatasi' }}</div>
                        </td>
                        <td class="py-4 pr-4">
                            <span class="font-semibold {{ $test->is_active ? 'text-emerald-600' : 'text-rose-600' }}">{{ $test->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="py-4">
                            <div class="flex flex-wrap gap-2 max-w-xl">
                                <a href="/admin/custom-tests/{{ $test->id }}" class="px-3 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white font-semibold">Buka Builder</a>
                                <form method="POST" action="/admin/custom-tests/{{ $test->id }}/toggle">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">{{ $test->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                <button type="button" data-edit-toggle="{{ $test->id }}" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Edit</button>
                                <form method="POST" action="/admin/custom-tests/{{ $test->id }}" onsubmit="return confirm('Hapus custom test ini? Semua dimensi, pertanyaan, opsi, dan rule posisi ikut terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 rounded-lg border border-rose-300 text-rose-700 font-semibold hover:bg-rose-50">Hapus</button>
                                </form>
                            </div>

                            <div id="edit-form-{{ $test->id }}" class="hidden mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3 max-w-xl">
                                <form method="POST" action="/admin/custom-tests/{{ $test->id }}" class="grid grid-cols-1 gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="name" value="{{ $test->name }}" required class="rounded-lg border-slate-300 text-sm">
                                    <input name="code" value="{{ $test->code }}" required class="rounded-lg border-slate-300 text-sm uppercase">
                                    <input type="number" name="time_limit_minutes" min="1" max="240" value="{{ $test->time_limit_minutes }}" class="rounded-lg border-slate-300 text-sm">
                                    <textarea name="description" rows="2" class="rounded-lg border-slate-300 text-sm">{{ $test->description }}</textarea>
                                    <textarea name="instructions" rows="2" class="rounded-lg border-slate-300 text-sm">{{ $test->instructions }}</textarea>
                                    <div>
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold">Simpan Edit</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-slate-500">Belum ada custom test.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-3 xl:hidden">
            @forelse($tests as $test)
                <article class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold text-slate-900">{{ $test->code }}</h3>
                        <span class="text-sm font-semibold {{ $test->is_active ? 'text-emerald-600' : 'text-rose-600' }}">{{ $test->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <div class="font-semibold text-slate-800">{{ $test->name }}</div>
                    <div class="text-sm text-slate-600">{{ $test->dimensions_count }} dimensi • {{ $test->questions_count }} pertanyaan • {{ $test->time_limit_minutes ? $test->time_limit_minutes . ' menit' : 'Tidak dibatasi' }}</div>
                    @if($test->description)<div class="text-sm text-slate-500">{{ $test->description }}</div>@endif

                    <div class="flex flex-wrap gap-2 pt-2">
                        <a href="/admin/custom-tests/{{ $test->id }}" class="px-3 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold">Buka Builder</a>
                        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/toggle">@csrf @method('PATCH')<button class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold">{{ $test->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                        <button type="button" data-edit-toggle="{{ $test->id }}" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold">Edit</button>
                        <form method="POST" action="/admin/custom-tests/{{ $test->id }}" onsubmit="return confirm('Hapus custom test ini? Semua dimensi, pertanyaan, opsi, dan rule posisi ikut terhapus.');">@csrf @method('DELETE')<button class="px-3 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-semibold">Hapus</button></form>
                    </div>

                    <div id="edit-form-mobile-{{ $test->id }}" class="hidden mt-2 rounded-xl border border-slate-200 bg-white p-3">
                        <form method="POST" action="/admin/custom-tests/{{ $test->id }}" class="grid grid-cols-1 gap-2">
                            @csrf @method('PATCH')
                            <input name="name" value="{{ $test->name }}" required class="rounded-lg border-slate-300 text-sm">
                            <input name="code" value="{{ $test->code }}" required class="rounded-lg border-slate-300 text-sm uppercase">
                            <input type="number" name="time_limit_minutes" min="1" max="240" value="{{ $test->time_limit_minutes }}" class="rounded-lg border-slate-300 text-sm">
                            <textarea name="description" rows="2" class="rounded-lg border-slate-300 text-sm">{{ $test->description }}</textarea>
                            <textarea name="instructions" rows="2" class="rounded-lg border-slate-300 text-sm">{{ $test->instructions }}</textarea>
                            <button type="submit" class="px-3 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold">Simpan Edit</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="text-slate-500">Belum ada custom test.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $tests->links() }}</div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-edit-toggle');
            const desktop = document.getElementById(`edit-form-${id}`);
            const mobile = document.getElementById(`edit-form-mobile-${id}`);
            if (desktop) desktop.classList.toggle('hidden');
            if (mobile) mobile.classList.toggle('hidden');
        });
    });
</script>
@endsection
