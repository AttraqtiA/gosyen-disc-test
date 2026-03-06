@extends('layouts.tw')

@section('title', 'Admin - Analytics & Export')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Admin Panel</h1>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="/admin/sessions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Kode Sesi Tes</a>
                <a href="/admin/positions" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Posisi & Kombinasi Tes</a>
                <a href="/admin/custom-tests" class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">Test Builder</a>
                <a href="/admin/analytics" class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-100 text-brand-700 border border-brand-200">Analytics & Export</a>
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
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Filter Rentang Tanggal</h2>
        <div class="mb-3 flex flex-wrap gap-2">
            <a href="/admin/analytics?preset=7d" class="px-3 py-2 rounded-xl text-sm font-semibold border {{ $filters['preset'] === '7d' ? 'bg-brand-100 text-brand-700 border-brand-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">7 Hari</a>
            <a href="/admin/analytics?preset=30d" class="px-3 py-2 rounded-xl text-sm font-semibold border {{ $filters['preset'] === '30d' ? 'bg-brand-100 text-brand-700 border-brand-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">30 Hari</a>
            <a href="/admin/analytics?preset=this_month" class="px-3 py-2 rounded-xl text-sm font-semibold border {{ $filters['preset'] === 'this_month' ? 'bg-brand-100 text-brand-700 border-brand-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">Bulan Ini</a>
        </div>
        <form method="GET" action="/admin/analytics" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <input type="hidden" name="preset" value="custom">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Mulai</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Sampai</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="w-full rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-xl bg-brand-500 text-white font-semibold hover:bg-brand-600">Terapkan</button>
                <a href="/admin/analytics" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
        <article class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4"><p class="text-sm text-slate-500">Total Sesi</p><p class="mt-1 text-2xl font-bold text-slate-900">{{ $summary['total_sessions'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4"><p class="text-sm text-slate-500">Peserta Mulai</p><p class="mt-1 text-2xl font-bold text-slate-900">{{ $summary['total_started'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4"><p class="text-sm text-slate-500">Selesai</p><p class="mt-1 text-2xl font-bold text-emerald-600">{{ $summary['total_completed'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4"><p class="text-sm text-slate-500">Timeout</p><p class="mt-1 text-2xl font-bold text-amber-600">{{ $summary['total_timeout'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4"><p class="text-sm text-slate-500">Completion Rate</p><p class="mt-1 text-2xl font-bold text-brand-700">{{ number_format($summary['completion_rate'], 2) }}%</p></article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Daily Trend</h2>
        <p class="text-sm text-slate-500 mb-4">Trend harian jumlah peserta mulai, selesai, dan timeout pada rentang tanggal terpilih.</p>
        <div class="h-80"><canvas id="trendChart"></canvas></div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7 space-y-4">
        <h2 class="text-2xl font-bold text-slate-900">Export Center</h2>
        <div class="flex flex-wrap gap-2">
            <a href="/admin/exports/tests.csv?type=ALL&{{ $queryString }}" class="px-4 py-2 rounded-xl bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600">Export Semua (CSV)</a>
            <a href="/admin/exports/tests.csv?type=DISC&{{ $queryString }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export DISC</a>
            <a href="/admin/exports/tests.csv?type=MBTI&{{ $queryString }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export MBTI</a>
            <a href="/admin/exports/tests.csv?type=OCEAN&{{ $queryString }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export OCEAN</a>
            <a href="/admin/exports/disc/questions.csv" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export Bank Soal DISC</a>
            <a href="/admin/exports/disc/manual.csv?{{ $queryString }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export Jawaban DISC (Manual)</a>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 md:p-7">
        <h2 class="text-2xl font-bold text-slate-900 mb-5">Analytics Per Sesi</h2>

        <div class="hidden xl:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">Kode</th><th class="py-3 pr-4">Nama Sesi</th><th class="py-3 pr-4">Tipe</th><th class="py-3 pr-4">Client</th><th class="py-3 pr-4">Mulai</th><th class="py-3 pr-4">Selesai</th><th class="py-3 pr-4">Timeout</th><th class="py-3 pr-4">In Progress</th><th class="py-3 pr-4">Avg Durasi</th><th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $item)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="py-4 pr-4 font-semibold">{{ $item['session']->code }}</td>
                            <td class="py-4 pr-4">{{ $item['session']->name }}</td>
                            <td class="py-4 pr-4">{{ $item['session']->test_type }}</td>
                            <td class="py-4 pr-4">{{ $item['session']->client->name ?? '-' }}</td>
                            <td class="py-4 pr-4">{{ $item['started'] }}</td>
                            <td class="py-4 pr-4 text-emerald-600 font-semibold">{{ $item['completed'] }}</td>
                            <td class="py-4 pr-4 text-amber-600 font-semibold">{{ $item['timeout'] }}</td>
                            <td class="py-4 pr-4">{{ $item['in_progress'] }}</td>
                            <td class="py-4 pr-4">{{ $item['avg_duration_seconds'] !== null ? $item['avg_duration_seconds'] . ' detik' : '-' }}</td>
                            <td class="py-4 space-x-2">
                                <a href="/admin/exports/sessions/{{ $item['session']->id }}.csv?{{ $queryString }}" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export Sesi</a>
                                @if($item['session']->test_type === 'DISC')
                                    <a href="/admin/exports/disc/manual.csv?session_id={{ $item['session']->id }}&{{ $queryString }}" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Export Manual DISC</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-6 text-slate-500">Belum ada data sesi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-3 xl:hidden">
            @forelse($rows as $item)
                <article class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-1">
                    <div class="font-bold text-slate-900">{{ $item['session']->code }} • {{ $item['session']->name }}</div>
                    <div class="text-sm text-slate-600">{{ $item['session']->test_type }} • {{ $item['session']->client->name ?? '-' }}</div>
                    <div class="text-sm text-slate-700">Mulai {{ $item['started'] }} | Selesai {{ $item['completed'] }} | Timeout {{ $item['timeout'] }} | In Progress {{ $item['in_progress'] }}</div>
                    <div class="text-sm text-slate-500">Avg Durasi: {{ $item['avg_duration_seconds'] !== null ? $item['avg_duration_seconds'] . ' detik' : '-' }}</div>
                    <div class="pt-1 flex flex-wrap gap-2">
                        <a href="/admin/exports/sessions/{{ $item['session']->id }}.csv?{{ $queryString }}" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold">Export Sesi</a>
                        @if($item['session']->test_type === 'DISC')
                            <a href="/admin/exports/disc/manual.csv?session_id={{ $item['session']->id }}&{{ $queryString }}" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold">Export Manual DISC</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="text-slate-500">Belum ada data sesi.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('trendChart');
        if (!ctx) return;

        const labels = @json($trend['labels']);
        const started = @json($trend['started']);
        const completed = @json($trend['completed']);
        const timeout = @json($trend['timeout']);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Mulai',
                        data: started,
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.15)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Selesai',
                        data: completed,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.12)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Timeout',
                        data: timeout,
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217, 119, 6, 0.12)',
                        tension: 0.3,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    })();
</script>
@endsection
