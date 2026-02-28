<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Tes</title>
    <style>
        :root { --bg:#030b1d; --panel:#0a1b39; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --accent:#5ce8ff; }
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg);padding:24px 16px}
        .wrap{max-width:1080px;margin:0 auto}
        h1{margin:0 0 8px;font-size:36px}.lead{color:var(--muted);max-width:900px;line-height:1.6}
        .toolbar{margin:16px 0;background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:12px;padding:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        .toolbar label{color:var(--muted);font-size:13px;font-weight:700}
        .toolbar select{background:#071327;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:8px 10px;min-width:180px}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:16px}
        .card{background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:14px;padding:16px}
        .chip{display:inline-block;padding:4px 10px;border-radius:999px;background:rgba(92,232,255,.12);border:1px solid rgba(92,232,255,.35);color:#bff7ff;font-weight:800;font-size:12px}
        h2{margin:8px 0 8px;font-size:24px}
        ul{margin:8px 0 0;padding-left:18px;color:var(--muted)}
        .section{margin-top:16px}
        .mono{font-family:ui-monospace,Menlo,monospace}
        @media (max-width:900px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Panduan & Tutorial Tes</h1>
    <p class="lead">Halaman ini menjelaskan arti hasil tes, cara membaca skor, dan bagaimana rekomendasi posisi digunakan sebagai alat bantu pengambilan keputusan.</p>

    <form class="toolbar" method="GET" action="/handbook/disc">
        <label for="type">Pilih Jenis Tes</label>
        <select id="type" name="type" onchange="this.form.submit()">
            @foreach($availableTypes as $item)
                <option value="{{ $item }}" @selected($type === $item)>{{ $item }}</option>
            @endforeach
        </select>
    </form>

    @if($type === 'DISC')
        <div class="grid">
            <div class="card">
                <span class="chip">D - Dominance</span>
                <h2>Dominan, tegas, fokus hasil</h2>
                <ul>
                    <li>Cenderung cepat mengambil keputusan.</li>
                    <li>Kuat di target, tantangan, dan kepemimpinan langsung.</li>
                    <li>Area pengembangan: kesabaran, kolaborasi, mendengar.</li>
                </ul>
            </div>
            <div class="card">
                <span class="chip">I - Influence</span>
                <h2>Persuasif, komunikatif, antusias</h2>
                <ul>
                    <li>Kuat membangun relasi dan memengaruhi orang.</li>
                    <li>Cocok untuk peran komunikasi dan engagement.</li>
                    <li>Area pengembangan: konsistensi dan perhatian detail.</li>
                </ul>
            </div>
            <div class="card">
                <span class="chip">S - Steadiness</span>
                <h2>Stabil, suportif, konsisten</h2>
                <ul>
                    <li>Nyaman pada proses yang jelas dan kerja tim harmonis.</li>
                    <li>Baik untuk peran support, koordinasi, layanan.</li>
                    <li>Area pengembangan: adaptasi perubahan cepat.</li>
                </ul>
            </div>
            <div class="card">
                <span class="chip">C - Compliance</span>
                <h2>Teliti, sistematis, berbasis standar</h2>
                <ul>
                    <li>Fokus pada akurasi, kualitas, dan analisis logis.</li>
                    <li>Kuat pada peran audit, QA, data, dan prosedur.</li>
                    <li>Area pengembangan: fleksibilitas dan kecepatan keputusan.</li>
                </ul>
            </div>
        </div>

        <div class="section card">
            <h2>Cara Membaca Hasil DISC</h2>
            <ul>
                <li>Skor <span class="mono">D/I/S/C</span> menunjukkan kecenderungan relatif, bukan nilai benar/salah.</li>
                <li>Tipe dominan adalah skor tertinggi, namun kombinasi empat dimensi tetap penting.</li>
                <li>Rekomendasi posisi dihitung dari kecocokan profil DISC responden vs profil posisi client.</li>
            </ul>
        </div>
    @else
        <div class="card">
            <h2>{{ $type }} - Dalam Pengembangan</h2>
            <p class="lead">Panduan detail untuk {{ $type }} akan ditambahkan setelah modul tes {{ $type }} aktif.</p>
            <ul>
                <li>Struktur rekomendasi posisi sudah disiapkan untuk multi test type.</li>
                <li>Admin dapat mulai menyiapkan posisi dan kombinasi profil tes di menu admin.</li>
                <li>Setelah modul aktif, halaman ini akan menampilkan interpretasi hasil spesifik {{ $type }}.</li>
            </ul>
        </div>
    @endif

    <div class="section card">
        <h2>Tentang Rekomendasi Posisi</h2>
        <ul>
            <li>Rekomendasi bersifat indikatif untuk membantu screening awal.</li>
            <li>Keputusan akhir tetap menggabungkan wawancara, kompetensi teknis, dan nilai budaya kerja.</li>
            <li>Admin dapat mengatur profil tiap posisi per jenis tes di menu <strong>Posisi & Kombinasi Tes</strong>.</li>
        </ul>
    </div>
</div>
</body>
</html>
