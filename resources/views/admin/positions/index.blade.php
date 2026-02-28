<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Posisi & Kombinasi Tes</title>
    <style>
        :root { --bg:#030b1d; --panel:#0a1b39; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --ok:#7ef9b6; --off:#ffc5de; --accent:#5ce8ff; --accent2:#35d0f5; }
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg);padding:24px 16px}
        .wrap{max-width:1280px;margin:0 auto}
        .top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px}
        .title{margin:0;font-size:34px}
        .nav{display:flex;gap:10px;flex-wrap:wrap}
        .tab{display:inline-block;padding:10px 14px;border-radius:10px;border:1px solid var(--line);text-decoration:none;color:var(--muted);font-weight:700}
        .tab.active{background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;border-color:transparent}
        .btn{border:0;border-radius:10px;background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;padding:10px 14px;font-weight:800;cursor:pointer}
        .btn-ghost{border:1px solid var(--line);border-radius:8px;background:#071327;color:var(--muted);padding:7px 10px;font-weight:700;cursor:pointer}
        .card{background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
        .full{grid-column:1 / -1}
        label{display:block;margin-bottom:6px;color:var(--muted);font-size:12px;font-weight:700}
        input,select,textarea{width:100%;background:#071327;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:10px;font-size:13px;min-height:42px}
        textarea{min-height:74px;resize:vertical}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid var(--line);padding:10px;font-size:13px;text-align:left;vertical-align:top}
        th{color:var(--muted)}
        .ok{color:var(--ok);font-weight:700}.off{color:var(--off);font-weight:700}
        .chip{display:inline-block;padding:3px 8px;border-radius:999px;border:1px solid var(--line);margin:2px 4px 2px 0;font-size:11px;color:var(--muted)}
        .inline{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
        .inline input[type="number"]{width:58px;min-height:34px;padding:6px 8px}
        .msg{margin-bottom:10px;color:var(--ok)} .err{margin-bottom:10px;color:var(--off)}
        @media (max-width:1100px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media (max-width:760px){.grid{grid-template-columns:1fr}.title{font-size:28px}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <h1 class="title">Admin Panel</h1>
            <div class="nav" style="margin-top:10px;">
                <a class="tab" href="/admin/sessions">Kode Sesi Tes</a>
                <a class="tab active" href="/admin/positions">Posisi & Kombinasi Tes</a>
                <a class="tab" href="/handbook/disc?type=DISC" target="_blank">Panduan Tes</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn" type="submit">Logout</button></form>
    </div>

    <div class="card">
        @if (session('success'))<div class="msg">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="err">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

        <h3 style="margin-top:0;">Tambah Posisi + Kombinasi Profil Tes</h3>
        <form method="POST" action="/admin/positions">
            @csrf
            <div class="grid">
                <div><label>Nama Posisi</label><input name="title" value="{{ old('title') }}" required></div>
                <div><label>Tipe Tes</label><select name="test_type"><option value="DISC">DISC</option><option value="MBTI">MBTI (rencana)</option><option value="16P">16 Personalities (rencana)</option></select></div>
                <div><label>Client (pilih)</label><select name="client_id"><option value="">-</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select></div>
                <div><label>Atau Nama Client Baru</label><input name="client_name" value="{{ old('client_name') }}"></div>

                <div><label>D Target</label><input type="number" min="0" max="100" name="d_target" value="{{ old('d_target', 25) }}" required></div>
                <div><label>I Target</label><input type="number" min="0" max="100" name="i_target" value="{{ old('i_target', 25) }}" required></div>
                <div><label>S Target</label><input type="number" min="0" max="100" name="s_target" value="{{ old('s_target', 25) }}" required></div>
                <div><label>C Target</label><input type="number" min="0" max="100" name="c_target" value="{{ old('c_target', 25) }}" required></div>

                <div class="full"><label><input type="checkbox" name="is_global" value="1" style="width:auto;min-height:0;"> Posisi global (berlaku untuk semua client)</label></div>
                <div class="full"><label>Deskripsi Posisi</label><textarea name="description">{{ old('description') }}</textarea></div>
                <div class="full"><label>Catatan Profil</label><textarea name="notes">{{ old('notes') }}</textarea></div>
                <div class="full"><button class="btn" type="submit">Simpan Posisi</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Daftar Posisi & Kombinasi</h3>
        <table>
            <thead>
                <tr><th>Posisi</th><th>Cakupan Client</th><th>Profil Tes</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($positions as $position)
                    <tr>
                        <td>
                            <strong>{{ $position->title }}</strong>
                            @if($position->description)<div style="margin-top:6px;color:var(--muted)">{{ $position->description }}</div>@endif
                        </td>
                        <td>
                            @if($position->is_global)
                                <span class="chip">Global (semua client)</span>
                            @endif

                            @php $attachedClients = $hasClientPosition ? $position->clients : collect(); @endphp
                            @if($hasClientPosition)
                                @foreach($attachedClients as $client)
                                    <span class="chip">{{ $client->name }}</span>
                                @endforeach
                            @endif

                            @if($position->client)
                                <span class="chip">Legacy: {{ $position->client->name }}</span>
                            @endif

                            @if($hasClientPosition)
                                <form method="POST" action="/admin/positions/{{ $position->id }}/clients" class="inline" style="margin-top:8px;">
                                    @csrf
                                    <select name="client_id" style="min-height:34px;max-width:220px;">
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-ghost">Tambah Client</button>
                                </form>
                            @else
                                <div class="chip" style="margin-top:8px;">Jalankan migrasi terbaru untuk fitur multi-client posisi</div>
                            @endif

                            @if($hasClientPosition && $attachedClients->isNotEmpty())
                                <div class="inline" style="margin-top:8px;">
                                    @foreach($attachedClients as $client)
                                        <form method="POST" action="/admin/positions/{{ $position->id }}/clients/{{ $client->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost">Lepas {{ $client->name }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            <form class="inline" method="POST" action="/admin/positions/{{ $position->id }}/profile">
                                @csrf
                                @method('PATCH')
                                <select name="test_type" style="min-height:34px; width:130px;">
                                    @php $profileType = $position->profile->test_type ?? 'DISC'; @endphp
                                    <option value="DISC" @selected($profileType === 'DISC')>DISC</option>
                                    <option value="MBTI" @selected($profileType === 'MBTI')>MBTI</option>
                                    <option value="16P" @selected($profileType === '16P')>16P</option>
                                </select>
                                <label>D<input type="number" min="0" max="100" name="d_target" value="{{ $position->profile->d_target ?? 25 }}"></label>
                                <label>I<input type="number" min="0" max="100" name="i_target" value="{{ $position->profile->i_target ?? 25 }}"></label>
                                <label>S<input type="number" min="0" max="100" name="s_target" value="{{ $position->profile->s_target ?? 25 }}"></label>
                                <label>C<input type="number" min="0" max="100" name="c_target" value="{{ $position->profile->c_target ?? 25 }}"></label>
                                <input type="text" name="notes" value="{{ $position->profile->notes ?? '' }}" placeholder="Catatan" style="width:180px;min-height:34px;padding:6px 8px;">
                                <button class="btn-ghost" type="submit">Update</button>
                            </form>
                        </td>
                        <td class="{{ $position->is_active ? 'ok' : 'off' }}">{{ $position->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td>
                            <form method="POST" action="/admin/positions/{{ $position->id }}/toggle">
                                @csrf
                                @method('PATCH')
                                <button class="btn" type="submit">{{ $position->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Belum ada posisi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">{{ $positions->links() }}</div>
    </div>
</div>
</body>
</html>
