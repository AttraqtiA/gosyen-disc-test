<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Test Builder</title>
    <style>
        :root { --bg:#030b1d; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --ok:#7ef9b6; --off:#ffc5de; --accent:#5ce8ff; --accent2:#35d0f5; }
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg);padding:24px 16px}
        .wrap{max-width:1240px;margin:0 auto}
        .top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:18px}
        .title{margin:0;font-size:32px}
        .nav{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
        .tab{display:inline-block;padding:10px 14px;border-radius:10px;border:1px solid var(--line);text-decoration:none;color:var(--muted);font-weight:700}
        .tab.active{background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;border-color:transparent}
        .card{background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .full{grid-column:1 / -1}
        label{display:block;margin-bottom:6px;color:var(--muted);font-size:12px;font-weight:700}
        input,textarea{width:100%;background:#071327;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:10px;font-size:13px;min-height:42px}
        textarea{min-height:80px;resize:vertical}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid var(--line);padding:10px;font-size:13px;text-align:left;vertical-align:top}
        th{color:var(--muted)}
        .btn{border:0;border-radius:10px;background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;padding:10px 14px;font-weight:800;cursor:pointer;text-decoration:none;display:inline-block}
        .btn-ghost{border:1px solid var(--line);border-radius:8px;background:#071327;color:var(--muted);padding:8px 10px;font-weight:700;cursor:pointer}
        .stack{display:flex;flex-direction:column;gap:8px;align-items:flex-start}
        .ok{color:var(--ok);font-weight:700}
        .off{color:var(--off);font-weight:700}
        .msg{margin-bottom:10px;color:var(--ok)}
        .err{margin-bottom:10px;color:var(--off)}
        @media (max-width:900px){.grid{grid-template-columns:1fr}.title{font-size:28px}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <h1 class="title">Admin Panel</h1>
            <div class="nav">
                <a class="tab" href="/admin/sessions">Kode Sesi Tes</a>
                <a class="tab" href="/admin/positions">Posisi & Kombinasi Tes</a>
                <a class="tab active" href="/admin/custom-tests">Test Builder</a>
                <a class="tab" href="/handbook?type=DISC" target="_blank">Panduan Tes</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn" type="submit">Logout</button></form>
    </div>

    <div class="card">
        @if (session('success'))<div class="msg">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="err">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

        <h3 style="margin-top:0;">Buat Test Baru (Custom)</h3>
        <form method="POST" action="/admin/custom-tests">
            @csrf
            <div class="grid">
                <div><label>Nama Test</label><input name="name" value="{{ old('name') }}" required></div>
                <div><label>Kode Test (unik)</label><input name="code" value="{{ old('code') }}" placeholder="CULTUREFIT" required></div>
                <div><label>Durasi (menit, opsional)</label><input type="number" name="time_limit_minutes" min="1" max="240" value="{{ old('time_limit_minutes') }}"></div>
                <div class="full"><label>Deskripsi</label><textarea name="description">{{ old('description') }}</textarea></div>
                <div class="full"><label>Instruksi Responden</label><textarea name="instructions">{{ old('instructions') }}</textarea></div>
                <div class="full"><button class="btn" type="submit">Buat Test</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Daftar Custom Test</h3>
        <table>
            <thead>
                <tr><th>Kode</th><th>Nama</th><th>Konten</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($tests as $test)
                    <tr>
                        <td><strong>{{ $test->code }}</strong></td>
                        <td>
                            <div><strong>{{ $test->name }}</strong></div>
                            @if($test->description)<div style="margin-top:6px;color:var(--muted)">{{ $test->description }}</div>@endif
                        </td>
                        <td>
                            <div>{{ $test->dimensions_count }} dimensi</div>
                            <div>{{ $test->questions_count }} pertanyaan</div>
                            <div>Durasi: {{ $test->time_limit_minutes ? $test->time_limit_minutes . ' menit' : 'Tidak dibatasi' }}</div>
                        </td>
                        <td class="{{ $test->is_active ? 'ok' : 'off' }}">{{ $test->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td>
                            <div class="stack">
                                <a class="btn" href="/admin/custom-tests/{{ $test->id }}">Buka Builder</a>
                                <form method="POST" action="/admin/custom-tests/{{ $test->id }}/toggle">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn" type="submit">{{ $test->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                <details>
                                    <summary style="cursor:pointer;color:var(--muted);font-weight:700;">Edit</summary>
                                    <form method="POST" action="/admin/custom-tests/{{ $test->id }}" style="margin-top:8px;">
                                        @csrf
                                        @method('PATCH')
                                        <div class="stack">
                                            <input name="name" value="{{ $test->name }}" required>
                                            <input name="code" value="{{ $test->code }}" required>
                                            <input type="number" name="time_limit_minutes" min="1" max="240" value="{{ $test->time_limit_minutes }}">
                                            <textarea name="description">{{ $test->description }}</textarea>
                                            <textarea name="instructions">{{ $test->instructions }}</textarea>
                                            <button class="btn-ghost" type="submit">Simpan Edit</button>
                                        </div>
                                    </form>
                                </details>
                                <form method="POST" action="/admin/custom-tests/{{ $test->id }}" onsubmit="return confirm('Hapus custom test ini? Semua dimensi, pertanyaan, opsi, dan rule posisi ikut terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Belum ada custom test.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">{{ $tests->links() }}</div>
    </div>
</div>
</body>
</html>
