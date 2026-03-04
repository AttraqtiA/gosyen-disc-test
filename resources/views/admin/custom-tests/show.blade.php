<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Builder - {{ $test->name }}</title>
    <style>
        :root { --bg:#030b1d; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --ok:#7ef9b6; --off:#ffc5de; --accent:#5ce8ff; --accent2:#35d0f5; }
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg);padding:24px 16px}
        .wrap{max-width:1300px;margin:0 auto}
        .top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:18px}
        .title{margin:0;font-size:30px}
        .muted{color:var(--muted)}
        .nav{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
        .tab{display:inline-block;padding:10px 14px;border-radius:10px;border:1px solid var(--line);text-decoration:none;color:var(--muted);font-weight:700}
        .tab.active{background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;border-color:transparent}
        .card{background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .grid-4{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}
        .full{grid-column:1 / -1}
        label{display:block;margin-bottom:6px;color:var(--muted);font-size:12px;font-weight:700}
        input,textarea,select{width:100%;background:#071327;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:10px;font-size:13px;min-height:42px}
        textarea{min-height:76px;resize:vertical}
        .btn{border:0;border-radius:10px;background:linear-gradient(180deg,var(--accent),var(--accent2));color:#032137;padding:10px 14px;font-weight:800;cursor:pointer}
        .pill{display:inline-block;padding:5px 10px;border-radius:999px;background:rgba(92,232,255,.12);border:1px solid rgba(92,232,255,.35);color:#bff7ff;font-size:12px;font-weight:700;margin-right:6px;margin-bottom:6px}
        .msg{margin-bottom:10px;color:var(--ok)}
        .err{margin-bottom:10px;color:var(--off)}
        .section-title{margin:0 0 12px}
        .list{margin:0;padding-left:18px}
        .q{border:1px solid var(--line);border-radius:10px;padding:12px;margin-top:10px;background:#071327}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid var(--line);padding:10px;font-size:13px;text-align:left;vertical-align:top}
        th{color:var(--muted)}
        @media (max-width:1000px){.grid,.grid-4{grid-template-columns:1fr}.title{font-size:26px}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <h1 class="title">Builder: {{ $test->name }} <span class="muted">({{ $test->code }})</span></h1>
            <div class="nav">
                <a class="tab" href="/admin/sessions">Kode Sesi Tes</a>
                <a class="tab" href="/admin/positions">Posisi & Kombinasi Tes</a>
                <a class="tab active" href="/admin/custom-tests">Test Builder</a>
            </div>
        </div>
        <div>
            <a class="tab" href="/admin/custom-tests">Kembali ke daftar</a>
        </div>
    </div>

    <div class="card">
        @if (session('success'))<div class="msg">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="err">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

        <h3 class="section-title">Info Test</h3>
        <div class="muted">{{ $test->description ?: 'Tidak ada deskripsi.' }}</div>
        <div style="margin-top:8px;">Durasi: {{ $test->time_limit_minutes ? $test->time_limit_minutes . ' menit' : 'Tidak dibatasi' }}</div>
        @if($test->instructions)<div style="margin-top:6px;" class="muted">Instruksi: {{ $test->instructions }}</div>@endif
    </div>

    <div class="card">
        <h3 class="section-title">1) Dimensi Skoring</h3>
        <div style="margin-bottom:10px;">
            @forelse($test->dimensions as $dimension)
                <span class="pill">{{ $dimension->code }} - {{ $dimension->name }} (w{{ $dimension->weight }})</span>
            @empty
                <span class="muted">Belum ada dimensi. Tambahkan dulu sebelum membuat logic skor jawaban.</span>
            @endforelse
        </div>

        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/dimensions">
            @csrf
            <div class="grid">
                <div><label>Kode Dimensi</label><input name="code" placeholder="LEADERSHIP" required></div>
                <div><label>Nama Dimensi</label><input name="name" placeholder="Leadership" required></div>
                <div><label>Bobot</label><input type="number" min="1" max="10" name="weight" value="1"></div>
                <div><label>Urutan</label><input type="number" min="1" max="999" name="sort_order"></div>
                <div class="full"><button class="btn" type="submit">Simpan Dimensi</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 class="section-title">2) Pertanyaan & Jawaban + Logic Skor</h3>
        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/questions">
            @csrf
            <div class="grid">
                <div class="full"><label>Pertanyaan</label><textarea name="question_text" required></textarea></div>
                <div><label>Urutan Soal</label><input type="number" min="1" max="9999" name="sort_order"></div>
                <div><label><input type="checkbox" name="is_required" value="1" checked style="width:auto;min-height:0;"> Wajib dijawab</label></div>
                <div class="full"><button class="btn" type="submit">Tambah Pertanyaan</button></div>
            </div>
        </form>

        @foreach($test->questions as $question)
            <div class="q">
                <div><strong>Q{{ $question->sort_order }}.</strong> {{ $question->question_text }}</div>
                <div class="muted" style="margin-top:4px;">Tipe: {{ $question->question_type }} | Required: {{ $question->is_required ? 'Ya' : 'Tidak' }}</div>

                <div style="margin-top:10px;">
                    <strong>Opsi yang sudah ada:</strong>
                    <ul class="list">
                        @forelse($question->options as $option)
                            <li>{{ $option->option_text }} <span class="muted">({{ json_encode($option->scores_json) }})</span></li>
                        @empty
                            <li class="muted">Belum ada opsi.</li>
                        @endforelse
                    </ul>
                </div>

                <form method="POST" action="/admin/custom-tests/{{ $test->id }}/questions/{{ $question->id }}/options" style="margin-top:10px;">
                    @csrf
                    <div class="grid">
                        <div class="full"><label>Teks Opsi Jawaban</label><input name="option_text" required></div>
                        <div><label>Urutan Opsi</label><input type="number" min="1" max="9999" name="sort_order"></div>
                        <div class="full">
                            <label>Logic Skor per Dimensi</label>
                            <div class="grid-4">
                                @foreach($test->dimensions as $dimension)
                                    <div>
                                        <label>{{ $dimension->code }}</label>
                                        <input type="number" name="score_{{ strtolower($dimension->code) }}" value="0">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="full"><button class="btn" type="submit">Tambah Opsi + Logic Skor</button></div>
                    </div>
                </form>
            </div>
        @endforeach
    </div>

    <div class="card">
        <h3 class="section-title">3) Rule Rekomendasi Posisi untuk Test Ini</h3>
        <form method="POST" action="/admin/custom-tests/{{ $test->id }}/position-rules">
            @csrf
            <div class="grid">
                <div>
                    <label>Pilih Posisi</label>
                    <select name="position_id" required>
                        <option value="">- pilih posisi -</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="full">
                    <label>Target Skor per Dimensi</label>
                    <div class="grid-4">
                        @foreach($test->dimensions as $dimension)
                            <div>
                                <label>{{ $dimension->code }}</label>
                                <input type="number" name="target_{{ strtolower($dimension->code) }}" value="0">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="full"><label>Catatan Rule</label><textarea name="notes"></textarea></div>
                <div class="full"><button class="btn" type="submit">Simpan Rule Posisi</button></div>
            </div>
        </form>

        <div style="margin-top:14px;">
            <table>
                <thead><tr><th>Posisi</th><th>Target Skor</th><th>Catatan</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($test->positionProfiles as $profile)
                        @php
                            $clientNames = $profile->position->clients->pluck('name')->implode(', ');
                            $fallbackClient = $profile->position->client->name ?? '-';
                            $displayClient = $clientNames !== '' ? $clientNames : $fallbackClient;
                        @endphp
                        <tr>
                            <td>{{ $profile->position->title }} <span class="muted">({{ $displayClient }})</span></td>
                            <td>{{ json_encode($profile->target_scores_json) }}</td>
                            <td>{{ $profile->notes ?: '-' }}</td>
                            <td>{{ $profile->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Belum ada rule posisi untuk test ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
