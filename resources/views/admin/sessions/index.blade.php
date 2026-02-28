<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kode Sesi Tes</title>
    <style>
        :root { --bg:#030b1d; --panel:#0a1b39; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --ok:#7ef9b6; --off:#ffc5de; --accent:#5ce8ff; --accent2:#35d0f5; }
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg);padding:24px 16px}
        .wrap{max-width:1250px;margin:0 auto}
        .top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px}
        .title{margin:0;font-size:36px}
        .nav{display:flex;gap:10px;flex-wrap:wrap}
        .tab{display:inline-block;padding:10px 14px;border-radius:10px;border:1px solid var(--line);text-decoration:none;color:var(--muted);font-weight:700}
        .tab.active{background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;border-color:transparent}
        .btn{border:0;border-radius:10px;background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;padding:10px 14px;font-weight:800;cursor:pointer}
        .card{background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
        .full{grid-column:1 / -1}
        label{display:block;margin-bottom:6px;color:var(--muted);font-size:12px;font-weight:700}
        input,select{width:100%;background:#071327;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:10px;font-size:13px;min-height:42px}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid var(--line);padding:10px;font-size:13px;text-align:left}
        th{color:var(--muted)}
        .ok{color:var(--ok);font-weight:700}.off{color:var(--off);font-weight:700}
        .msg{margin-bottom:10px;color:var(--ok)} .err{margin-bottom:10px;color:var(--off)}
        @media (max-width:900px){.grid{grid-template-columns:1fr}.title{font-size:28px}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <h1 class="title">Admin Panel</h1>
            <div class="nav" style="margin-top:10px;">
                <a class="tab active" href="/admin/sessions">Kode Sesi Tes</a>
                <a class="tab" href="/admin/positions">Posisi & Kombinasi DISC</a>
                <a class="tab" href="/handbook/disc?type=DISC" target="_blank">Panduan Tes</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn" type="submit">Logout</button></form>
    </div>

    <div class="card">
        @if (session('success'))<div class="msg">{{ session('success') }}</div>@endif
        @if ($errors->any())
            <div class="err">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        @endif

        <h3 style="margin-top:0;">Buat Kode Akses Sesi</h3>
        <form method="POST" action="/admin/sessions">
            @csrf
            <div class="grid">
                <div><label>Nama Sesi</label><input name="name" value="{{ old('name') }}" required></div>
                <div><label>Kode (custom)</label><input name="code" value="{{ old('code') }}" placeholder="DISC-MEI26" required></div>
                <div><label>Tipe Tes</label><select name="test_type"><option value="DISC">DISC</option><option value="MBTI">MBTI (persiapan)</option><option value="OTHER">Other (persiapan)</option></select></div>
                <div><label>Client (pilih)</label><select name="client_id"><option value="">-</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select></div>
                <div><label>Atau Nama Client Baru</label><input name="client_name" value="{{ old('client_name') }}"></div>
                <div><label>Kedaluwarsa (opsional)</label><input name="expires_at" type="datetime-local" value="{{ old('expires_at') }}"></div>
                <div class="full"><button class="btn" type="submit">Buat Kode Sesi</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Daftar Sesi</h3>
        <table>
            <thead>
                <tr><th>Kode</th><th>Nama Sesi</th><th>Tipe</th><th>Client</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr>
                        <td><strong>{{ $session->code }}</strong></td>
                        <td>{{ $session->name }}</td>
                        <td>{{ $session->test_type }}</td>
                        <td>{{ $session->client->name ?? '-' }}</td>
                        <td class="{{ $session->is_active ? 'ok' : 'off' }}">{{ $session->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td>
                            <form method="POST" action="/admin/sessions/{{ $session->id }}/toggle">
                                @csrf
                                @method('PATCH')
                                <button class="btn" type="submit">{{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Belum ada sesi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">{{ $sessions->links() }}</div>
    </div>
</div>
</body>
</html>
