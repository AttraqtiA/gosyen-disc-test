<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Tes Kepribadian</title>
    <style>
        :root { --bg:#030b1d; --panel:#0a1b39; --line:#1f3d68; --text:#e6f1ff; --muted:#94acd0; --accent:#5ce8ff; }
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 12% 18%, rgba(73,224,255,.18), transparent 38%),radial-gradient(circle at 90% 12%, rgba(73,126,255,.18), transparent 34%),var(--bg);padding:24px 16px}
        .wrap{max-width:1080px;margin:0 auto}
        h1{margin:0 0 8px;font-size:34px;line-height:1.2}
        .lead{color:var(--muted);max-width:920px;line-height:1.6}
        .toolbar{margin:16px 0;background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:12px;padding:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        .toolbar label{color:var(--muted);font-size:13px;font-weight:700}
        .toolbar select{background:#071327;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:8px 10px;min-width:180px}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:16px}
        .card{background:linear-gradient(180deg, rgba(13,32,66,.96), rgba(8,21,45,.98));border:1px solid var(--line);border-radius:14px;padding:16px}
        .chip{display:inline-block;padding:4px 10px;border-radius:999px;background:rgba(92,232,255,.12);border:1px solid rgba(92,232,255,.35);color:#bff7ff;font-weight:800;font-size:12px}
        h2{margin:8px 0 8px;font-size:22px}
        h3{margin:8px 0;font-size:18px}
        ul{margin:8px 0 0;padding-left:18px;color:var(--muted);line-height:1.5}
        .section{margin-top:16px}
        .mono{font-family:ui-monospace,Menlo,monospace;color:#bff7ff}
        @media (max-width:900px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Panduan & Tutorial Tes Kepribadian</h1>
    <p class="lead">Halaman ini menjelaskan arti hasil tes, formula perhitungan, dan cara sistem merekomendasikan posisi kerja secara terukur.</p>

    <form class="toolbar" method="GET" action="/handbook">
        <label for="type">Pilih Jenis Tes</label>
        <select id="type" name="type" onchange="this.form.submit()">
            @foreach($availableTypes as $item)
                <option value="{{ $item }}" @selected($type === $item)>{{ $item }}</option>
            @endforeach
        </select>
    </form>

    @if($type === 'DISC')
        <div class="grid">
            <div class="card"><span class="chip">D - Dominance</span><h2>Dominan, tegas, fokus hasil</h2><ul><li>Cepat mengambil keputusan dan berorientasi target.</li><li>Kuat untuk peran yang butuh dorongan hasil.</li><li>Perlu menjaga kesabaran dan empati tim.</li></ul></div>
            <div class="card"><span class="chip">I - Influence</span><h2>Persuasif, komunikatif, antusias</h2><ul><li>Kuat dalam relasi, presentasi, dan engagement.</li><li>Cocok untuk peran sales, marketing, komunikasi.</li><li>Perlu menjaga konsistensi dan detail.</li></ul></div>
            <div class="card"><span class="chip">S - Steadiness</span><h2>Stabil, suportif, konsisten</h2><ul><li>Tenang, kooperatif, dan suka ritme kerja stabil.</li><li>Cocok untuk support, operasional, koordinasi.</li><li>Perlu adaptasi pada perubahan cepat.</li></ul></div>
            <div class="card"><span class="chip">C - Compliance</span><h2>Teliti, sistematis, berbasis standar</h2><ul><li>Kuat pada akurasi, kualitas, dan analisis.</li><li>Cocok untuk audit, QA, data, compliance.</li><li>Perlu melatih fleksibilitas dalam situasi ambigu.</li></ul></div>
        </div>

        <div class="section card">
            <h2>Formula Hasil DISC</h2>
            <ul>
                <li>Setiap nomor: responden pilih <strong>Paling menggambarkan (P)</strong> dan <strong>Paling tidak menggambarkan (K)</strong>.</li>
                <li>Setiap pilihan P menambah <span class="mono">+1</span> pada tipe huruf pernyataan tersebut (D/I/S/C).</li>
                <li>Setiap pilihan K mengurangi <span class="mono">-1</span> pada tipe huruf pernyataan tersebut.</li>
                <li>Skor akhir: <span class="mono">D, I, S, C</span>. Tipe dominan adalah skor tertinggi.</li>
            </ul>
        </div>
    @elseif($type === 'MBTI')
        <div class="grid">
            <div class="card"><span class="chip">E vs I</span><h2>Sumber Energi</h2><ul><li><strong>E (Extraversion)</strong>: energi dari interaksi eksternal.</li><li><strong>I (Introversion)</strong>: energi dari refleksi internal.</li></ul></div>
            <div class="card"><span class="chip">S vs N</span><h2>Cara Memproses Informasi</h2><ul><li><strong>S (Sensing)</strong>: fakta konkret, detail, realitas kini.</li><li><strong>N (Intuition)</strong>: pola, ide, makna, kemungkinan.</li></ul></div>
            <div class="card"><span class="chip">T vs F</span><h2>Gaya Pengambilan Keputusan</h2><ul><li><strong>T (Thinking)</strong>: logika, objektivitas, konsistensi aturan.</li><li><strong>F (Feeling)</strong>: nilai personal, empati, keharmonisan.</li></ul></div>
            <div class="card"><span class="chip">J vs P</span><h2>Gaya Menjalankan Aktivitas</h2><ul><li><strong>J (Judging)</strong>: terstruktur, terencana, closure cepat.</li><li><strong>P (Perceiving)</strong>: fleksibel, adaptif, opsi terbuka.</li></ul></div>
        </div>

        <div class="section card">
            <h2>Formula Hasil MBTI</h2>
            <ul>
                <li>Setiap soal memiliki 2 opsi, masing-masing mewakili satu trait.</li>
                <li>Pilihan responden menambah <span class="mono">+1</span> pada trait terpilih.</li>
                <li>Total dihitung per pasangan: <span class="mono">E-I</span>, <span class="mono">S-N</span>, <span class="mono">T-F</span>, <span class="mono">J-P</span>.</li>
                <li>Kode tipe dibentuk dari skor tertinggi tiap pasangan, contoh: <span class="mono">ENTJ</span>, <span class="mono">ISFP</span>.</li>
                <li>Jika skor pasangan seri, sistem memakai huruf pertama pasangan sebagai tie-break: <span class="mono">E, S, T, J</span>.</li>
            </ul>
        </div>

        <div class="section card">
            <h2>Catatan MBTI</h2>
            <ul>
                <li>Platform ini menggunakan pendekatan MBTI 4 dimensi untuk pemetaan perilaku kerja.</li>
                <li>Hasil digunakan sebagai pemetaan preferensi kerja, bukan label mutlak.</li>
            </ul>
        </div>
    @endif

    <div class="section card">
        <h2>Formula Rekomendasi Posisi (Semua Jenis Tes)</h2>
        <ul>
            <li>Setiap posisi memiliki <strong>profil target</strong> per jenis tes (DISC/MBTI).</li>
            <li>Sistem membandingkan vektor skor responden dengan vektor target posisi.</li>
            <li>Semakin kecil selisih total antar dimensi, semakin tinggi <strong>match score</strong>.</li>
            <li>Hasil diurutkan dari skor kecocokan tertinggi sebagai rekomendasi utama.</li>
        </ul>
    </div>

    <div class="section card">
        <h2>Contoh Penggunaan Rekomendasi</h2>
        <ul>
            <li><strong>Screening awal rekrutmen:</strong> membantu shortlist kandidat lebih objektif.</li>
            <li><strong>Penempatan internal:</strong> memetakan kecocokan karyawan ke role tertentu.</li>
            <li><strong>Pengembangan tim:</strong> memahami komposisi perilaku dan gaya kerja tim.</li>
            <li>Keputusan final tetap dikombinasikan dengan wawancara, kompetensi teknis, dan budaya kerja.</li>
        </ul>
    </div>
</div>
</body>
</html>
