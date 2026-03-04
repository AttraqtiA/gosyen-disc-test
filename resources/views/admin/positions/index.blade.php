@extends('layouts.tw')

@section('title', 'Admin - Posisi & Kombinasi Tes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Admin Panel</h1>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="/admin/sessions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Kode Sesi Tes</a>
                <a href="/admin/positions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-100 text-brand-700 border border-brand-200">Posisi & Kombinasi Tes</a>
                <a href="/admin/custom-tests" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Test Builder</a>
                <a href="/admin/analytics" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Analytics & Export</a>
                <a href="/admin/reviews" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Review Essay</a>
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
                <svg data-theme-icon="moon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3c-.12.58-.18 1.18-.18 1.79A7.2 7.2 0 0 0 18.2 12c.61 0 1.21-.06 1.8-.18z" />
                </svg>
                <svg data-theme-icon="sun" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4" />
                    <path d="M12 2v2.2M12 19.8V22M4.2 4.2l1.6 1.6M18.2 18.2l1.6 1.6M2 12h2.2M19.8 12H22M4.2 19.8l1.6-1.6M18.2 5.8l1.6-1.6" />
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

        <h2 class="text-2xl font-bold text-slate-900 mb-5">Tambah Posisi + Kombinasi Profil</h2>

        <form method="POST" action="/admin/positions" id="create-position-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Posisi</label>
                <input name="title" value="{{ old('title') }}" required class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Tes</label>
                <select name="test_type" id="create-test-type" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                    <option value="DISC" @selected(old('test_type') === 'DISC')>DISC</option>
                    <option value="MBTI" @selected(old('test_type') === 'MBTI')>MBTI</option>
                    <option value="OCEAN" @selected(old('test_type') === 'OCEAN')>OCEAN (Big 5)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Client (pilih)</label>
                <select name="client_id" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                    <option value="">-</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Atau Nama Client Baru</label>
                <input name="client_name" value="{{ old('client_name') }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>

            <div class="md:col-span-2 hidden grid-cols-2 lg:grid-cols-4 gap-3 profile-block" data-profile="DISC">
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">D Target</label><input type="number" min="0" max="100" name="d_target" value="{{ old('d_target', 25) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">I Target</label><input type="number" min="0" max="100" name="i_target" value="{{ old('i_target', 25) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">S Target</label><input type="number" min="0" max="100" name="s_target" value="{{ old('s_target', 25) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">C Target</label><input type="number" min="0" max="100" name="c_target" value="{{ old('c_target', 25) }}" class="w-full rounded-xl border-slate-300"></div>
            </div>

            <div class="md:col-span-2 hidden grid-cols-2 lg:grid-cols-4 gap-3 profile-block" data-profile="MBTI">
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">E</label><input type="number" min="0" max="100" name="e_target" value="{{ old('e_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">I</label><input type="number" min="0" max="100" name="i_target" value="{{ old('i_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">S</label><input type="number" min="0" max="100" name="s_target" value="{{ old('s_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">N</label><input type="number" min="0" max="100" name="n_target" value="{{ old('n_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">T</label><input type="number" min="0" max="100" name="t_target" value="{{ old('t_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">F</label><input type="number" min="0" max="100" name="f_target" value="{{ old('f_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">J</label><input type="number" min="0" max="100" name="j_target" value="{{ old('j_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">P</label><input type="number" min="0" max="100" name="p_target" value="{{ old('p_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
            </div>

            <div class="md:col-span-2 hidden grid-cols-2 lg:grid-cols-5 gap-3 profile-block" data-profile="OCEAN">
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">O</label><input type="number" min="0" max="100" name="o_target" value="{{ old('o_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">C</label><input type="number" min="0" max="100" name="c_target" value="{{ old('c_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">E</label><input type="number" min="0" max="100" name="e_target" value="{{ old('e_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">A</label><input type="number" min="0" max="100" name="a_target" value="{{ old('a_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-1">N</label><input type="number" min="0" max="100" name="n_target" value="{{ old('n_target', 50) }}" class="w-full rounded-xl border-slate-300"></div>
            </div>

            <div class="md:col-span-2 flex items-center gap-2 pt-1">
                <input id="is_global" type="checkbox" name="is_global" value="1" @checked(old('is_global')) class="rounded border-slate-300 text-brand-500 focus:ring-brand-400">
                <label for="is_global" class="text-sm text-slate-700 font-medium">Posisi global (berlaku untuk semua client)</label>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Posisi</label>
                <textarea name="description" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">{{ old('description') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Profil</label>
                <textarea name="notes" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">{{ old('notes') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold">Simpan Posisi</button>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-2xl font-bold text-slate-900 mb-5">Daftar Posisi & Kombinasi</h2>

        <div class="hidden xl:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">Posisi</th>
                        <th class="py-3 pr-4">Client</th>
                        <th class="py-3 pr-4">Profil</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($positions as $position)
                    @php
                        $discProfile = $position->profile;
                        $mbtiProfile = $position->mbtiProfiles->first();
                        $oceanProfile = $position->oceanProfiles->first();
                        $activeType = $discProfile ? 'DISC' : ($mbtiProfile ? 'MBTI' : ($oceanProfile ? 'OCEAN' : 'DISC'));
                        $attachedClients = $hasClientPosition ? $position->clients : collect();
                    @endphp
                    <tr class="border-b border-slate-100 align-top">
                        <td class="py-4 pr-4">
                            <div class="font-semibold">{{ $position->title }}</div>
                            @if($position->description)<div class="text-slate-500 mt-1">{{ $position->description }}</div>@endif
                        </td>
                        <td class="py-4 pr-4 space-y-2">
                            @if($position->is_global)<span class="inline-flex px-2 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-semibold">Global</span>@endif
                            @foreach($attachedClients as $client)
                                <span class="inline-flex px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">{{ $client->name }}</span>
                            @endforeach
                            @if($position->client)
                                <span class="inline-flex px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">Legacy: {{ $position->client->name }}</span>
                            @endif

                            @if($hasClientPosition)
                                <form method="POST" action="/admin/positions/{{ $position->id }}/clients" class="flex gap-2 pt-2">
                                    @csrf
                                    <select name="client_id" class="rounded-lg border-slate-300 text-sm">
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold">Tambah</button>
                                </form>
                            @endif
                        </td>
                        <td class="py-4 pr-4">
                            <form method="POST" action="/admin/positions/{{ $position->id }}/profile" class="space-y-2 max-w-md">
                                @csrf @method('PATCH')
                                <input type="hidden" name="test_type" value="{{ $activeType }}">
                                <div class="inline-flex px-2 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-semibold">{{ $activeType }}</div>

                                @if($activeType === 'DISC')
                                    <div class="grid grid-cols-4 gap-2">
                                        <input type="number" min="0" max="100" name="d_target" value="{{ $discProfile->d_target ?? 25 }}" class="rounded-lg border-slate-300 text-xs" placeholder="D">
                                        <input type="number" min="0" max="100" name="i_target" value="{{ $discProfile->i_target ?? 25 }}" class="rounded-lg border-slate-300 text-xs" placeholder="I">
                                        <input type="number" min="0" max="100" name="s_target" value="{{ $discProfile->s_target ?? 25 }}" class="rounded-lg border-slate-300 text-xs" placeholder="S">
                                        <input type="number" min="0" max="100" name="c_target" value="{{ $discProfile->c_target ?? 25 }}" class="rounded-lg border-slate-300 text-xs" placeholder="C">
                                    </div>
                                @elseif($activeType === 'MBTI')
                                    <div class="grid grid-cols-4 gap-2">
                                        <input type="number" min="0" max="100" name="e_target" value="{{ $mbtiProfile->e_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="E">
                                        <input type="number" min="0" max="100" name="i_target" value="{{ $mbtiProfile->i_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="I">
                                        <input type="number" min="0" max="100" name="s_target" value="{{ $mbtiProfile->s_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="S">
                                        <input type="number" min="0" max="100" name="n_target" value="{{ $mbtiProfile->n_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="N">
                                        <input type="number" min="0" max="100" name="t_target" value="{{ $mbtiProfile->t_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="T">
                                        <input type="number" min="0" max="100" name="f_target" value="{{ $mbtiProfile->f_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="F">
                                        <input type="number" min="0" max="100" name="j_target" value="{{ $mbtiProfile->j_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="J">
                                        <input type="number" min="0" max="100" name="p_target" value="{{ $mbtiProfile->p_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="P">
                                    </div>
                                @else
                                    <div class="grid grid-cols-5 gap-2">
                                        <input type="number" min="0" max="100" name="o_target" value="{{ $oceanProfile->o_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="O">
                                        <input type="number" min="0" max="100" name="c_target" value="{{ $oceanProfile->c_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="C">
                                        <input type="number" min="0" max="100" name="e_target" value="{{ $oceanProfile->e_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="E">
                                        <input type="number" min="0" max="100" name="a_target" value="{{ $oceanProfile->a_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="A">
                                        <input type="number" min="0" max="100" name="n_target" value="{{ $oceanProfile->n_target ?? 50 }}" class="rounded-lg border-slate-300 text-xs" placeholder="N">
                                    </div>
                                @endif

                                <input type="text" name="notes" value="{{ $discProfile->notes ?? $mbtiProfile->notes ?? $oceanProfile->notes ?? '' }}" class="rounded-lg border-slate-300 text-sm" placeholder="Catatan">
                                <button class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold">Update</button>
                            </form>
                        </td>
                        <td class="py-4 pr-4"><span class="font-semibold {{ $position->is_active ? 'text-emerald-600' : 'text-rose-600' }}">{{ $position->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="py-4">
                            <div class="space-y-2">
                                <form method="POST" action="/admin/positions/{{ $position->id }}/toggle">@csrf @method('PATCH')<button class="px-3 py-2 rounded-lg bg-brand-500 text-white font-semibold">{{ $position->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                                @if($hasClientPosition && $attachedClients->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($attachedClients as $client)
                                            <form method="POST" action="/admin/positions/{{ $position->id }}/clients/{{ $client->id }}">@csrf @method('DELETE')<button class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold">Lepas {{ $client->name }}</button></form>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-slate-500">Belum ada posisi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-3 xl:hidden">
            @forelse($positions as $position)
                @php
                    $discProfile = $position->profile;
                    $mbtiProfile = $position->mbtiProfiles->first();
                    $oceanProfile = $position->oceanProfiles->first();
                    $activeType = $discProfile ? 'DISC' : ($mbtiProfile ? 'MBTI' : ($oceanProfile ? 'OCEAN' : 'DISC'));
                @endphp
                <article class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold">{{ $position->title }}</h3>
                        <span class="text-sm font-semibold {{ $position->is_active ? 'text-emerald-600' : 'text-rose-600' }}">{{ $position->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <div class="text-sm text-slate-500">{{ $position->description }}</div>
                    <div class="text-xs inline-flex px-2 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700">{{ $activeType }}</div>
                    <div class="pt-2"><form method="POST" action="/admin/positions/{{ $position->id }}/toggle">@csrf @method('PATCH')<button class="px-3 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold">{{ $position->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form></div>
                </article>
            @empty
                <div class="text-slate-500">Belum ada posisi.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $positions->links() }}</div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const select = document.getElementById('create-test-type');
        const blocks = document.querySelectorAll('.profile-block');

        function render() {
            const value = (select.value || 'DISC').toUpperCase();
            blocks.forEach((block) => {
                const show = block.dataset.profile === value;
                block.classList.toggle('hidden', !show);
                block.classList.toggle('grid', show);
                block.querySelectorAll('input').forEach((input) => { input.disabled = !show; });
            });
        }

        select.addEventListener('change', render);
        render();
    })();
</script>
@endsection
