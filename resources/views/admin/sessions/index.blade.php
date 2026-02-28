<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kode Sesi</title>
    <style>
        body { margin:0; font-family:Arial,sans-serif; color:#e6f1ff; background:#030b1d; padding:20px; }
        .wrap { max-width:1100px; margin:0 auto; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
        .card { background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98)); border:1px solid #1f3d68; border-radius:14px; padding:16px; margin-bottom:16px; }
        .grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
        .full { grid-column:1 / -1; }
        label { display:block; margin-bottom:4px; color:#94acd0; font-size:12px; font-weight:700; }
        input, select { width:100%; background:#071327; color:#e6f1ff; border:1px solid #1f3d68; border-radius:8px; padding:10px; font-size:13px; }
        button { border:0; border-radius:8px; background:linear-gradient(180deg,#5ce8ff,#35d0f5); color:#032137; padding:9px 12px; font-weight:800; cursor:pointer; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #1f3d68; padding:10px; font-size:13px; text-align:left; }
        th { color:#94acd0; }
        .ok { color:#7ef9b6; }
        .off { color:#ffc5de; }
        .msg { margin-bottom:8px; color:#7ef9b6; }
        .err { margin-bottom:8px; color:#ffc5de; }
        @media (max-width: 900px) { .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <h1>Admin - Kode Sesi Tes</h1>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
    </div>

    <div class="card">
        @if (session('success'))<div class="msg">{{ session('success') }}</div>@endif
        @if ($errors->any())
            <div class="err">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="/admin/sessions">
            @csrf
            <div class="grid">
                <div><label>Nama Sesi</label><input name="name" value="{{ old('name') }}" required></div>
                <div><label>Kode (custom)</label><input name="code" value="{{ old('code') }}" placeholder="DISC-MEI26" required></div>
                <div><label>Tipe Tes</label><select name="test_type"><option value="DISC">DISC</option></select></div>
                <div><label>Client (pilih)</label><select name="client_id"><option value="">-</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select></div>
                <div><label>Atau Nama Client Baru</label><input name="client_name" value="{{ old('client_name') }}"></div>
                <div><label>Kedaluwarsa (opsional)</label><input name="expires_at" type="datetime-local" value="{{ old('expires_at') }}"></div>
                <div class="full"><button type="submit">Buat Kode Sesi</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Kode</th><th>Nama Sesi</th><th>Tipe</th><th>Client</th><th>Status</th><th>Aksi</th>
                </tr>
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
                                <button type="submit">{{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
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
