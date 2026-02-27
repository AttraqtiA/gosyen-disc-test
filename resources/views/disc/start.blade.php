<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Kepribadian</title>
    <style>
        :root {
            --bg: #030b1d;
            --panel: #08152d;
            --panel-soft: #0d2042;
            --line: #1f3d68;
            --text: #e6f1ff;
            --muted: #94acd0;
            --accent: #5ce8ff;
            --accent-2: #35d0f5;
            --danger-bg: #2a1020;
            --danger-line: #7b2b52;
            --danger-text: #ffc5de;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% 18%, rgba(73, 224, 255, 0.18), transparent 38%),
                radial-gradient(circle at 90% 12%, rgba(73, 126, 255, 0.18), transparent 34%),
                var(--bg);
            padding: 32px 16px;
        }

        .container {
            max-width: 920px;
            margin: 0 auto;
            background: linear-gradient(180deg, rgba(13, 32, 66, 0.96), rgba(8, 21, 45, 0.98));
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .brand img { width: 52px; height: 52px; object-fit: contain; }
        .brand h1 { margin: 0; font-size: 28px; letter-spacing: 0.2px; }
        .subtitle { margin: 0 0 18px; color: var(--muted); }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .full { grid-column: 1 / -1; }

        label { font-size: 13px; color: var(--muted); font-weight: 700; letter-spacing: 0.2px; }

        input, select {
            width: 100%;
            background: #071327;
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
        }

        input:focus, select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(92, 232, 255, 0.2);
        }

        .errors {
            margin: 0 0 16px;
            background: var(--danger-bg);
            border: 1px solid var(--danger-line);
            color: var(--danger-text);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
        }

        .note {
            margin-top: 16px;
            font-size: 13px;
            color: var(--muted);
        }

        .btn {
            margin-top: 18px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            color: #032137;
            padding: 12px 16px;
            font-weight: 800;
            cursor: pointer;
        }

        @media (max-width: 780px) {
            .grid { grid-template-columns: 1fr; }
            .container { padding: 20px; }
            .brand h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <img src="/images/GosyenLogo-removebg-preview.png" alt="Gosyen Logo">
            <h1>Tes Kepribadian</h1>
        </div>
        <p class="subtitle">Isi data responden terlebih dahulu. Tes ini tidak memerlukan akun.</p>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/start">
            @csrf
            <div class="grid">
                <div class="field full">
                    <label for="nama">Nama Lengkap *</label>
                    <input id="nama" name="nama" value="{{ old('nama') }}" required>
                </div>

                <div class="field">
                    <label for="institusi_perusahaan">Institusi/Perusahaan *</label>
                    <input id="institusi_perusahaan" name="institusi_perusahaan" value="{{ old('institusi_perusahaan') }}" required>
                </div>

                <div class="field">
                    <label for="departemen_divisi">Departemen/Divisi *</label>
                    <input id="departemen_divisi" name="departemen_divisi" value="{{ old('departemen_divisi') }}" required>
                </div>

                <div class="field">
                    <label for="jabatan_saat_ini">Jabatan Saat Ini</label>
                    <input id="jabatan_saat_ini" name="jabatan_saat_ini" value="{{ old('jabatan_saat_ini') }}">
                </div>

                <div class="field">
                    <label for="usia">Usia *</label>
                    <input id="usia" type="number" min="15" max="80" name="usia" value="{{ old('usia') }}" required>
                </div>

                <div class="field">
                    <label for="jenis_kelamin">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih...</option>
                        <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                    </select>
                </div>

                <div class="field">
                    <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                    <input id="pendidikan_terakhir" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir') }}">
                </div>

                <div class="field">
                    <label for="lama_pengalaman_kerja">Pengalaman Kerja (tahun)</label>
                    <input id="lama_pengalaman_kerja" type="number" min="0" max="60" name="lama_pengalaman_kerja" value="{{ old('lama_pengalaman_kerja') }}">
                </div>

                <div class="field">
                    <label for="lokasi_kota">Lokasi/Kota</label>
                    <input id="lokasi_kota" name="lokasi_kota" value="{{ old('lokasi_kota') }}">
                </div>

                <div class="field">
                    <label for="tujuan_tes">Tujuan Tes</label>
                    <select id="tujuan_tes" name="tujuan_tes">
                        <option value="">Pilih...</option>
                        <option value="Rekrutmen" @selected(old('tujuan_tes') === 'Rekrutmen')>Rekrutmen</option>
                        <option value="Promosi" @selected(old('tujuan_tes') === 'Promosi')>Promosi</option>
                        <option value="Pengembangan Tim" @selected(old('tujuan_tes') === 'Pengembangan Tim')>Pengembangan Tim</option>
                    </select>
                </div>

                <div class="field">
                    <label for="email">Email (opsional)</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="field">
                    <label for="nomor_hp">Nomor HP (opsional)</label>
                    <input id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp') }}">
                </div>
            </div>

            <p class="note">Durasi tes maksimal 15 menit. Mohon isi dengan jujur agar hasil menggambarkan karakter asli Anda.</p>
            <button class="btn" type="submit">Mulai Tes</button>
        </form>
    </div>
</body>
</html>
