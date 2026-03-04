@extends('layouts.tw')

@section('title', 'Panduan Tes Kepribadian')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Panduan & Tutorial Tes Kepribadian</h1>
        <p class="mt-2 text-slate-600">Halaman ini menjelaskan arti hasil tes, formula perhitungan, dan cara sistem merekomendasikan posisi kerja secara terukur.</p>

        <form class="mt-5 flex flex-col sm:flex-row sm:items-center gap-3" method="GET" action="/handbook">
            <label for="type" class="text-sm font-semibold text-slate-700">Pilih Jenis Tes</label>
            <select id="type" name="type" onchange="this.form.submit()" class="w-full sm:w-64 rounded-xl border-slate-300 focus:border-brand-400 focus:ring-brand-400">
                @foreach($availableTypes as $item)
                    <option value="{{ $item }}" @selected($type === $item)>{{ $item }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($type === 'DISC')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">D - Dominance</span><h2 class="mt-3 text-lg font-bold text-slate-900">Dominan, tegas, fokus hasil</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Cepat mengambil keputusan dan berorientasi target.</li><li>Kuat untuk peran yang butuh dorongan hasil.</li><li>Perlu menjaga kesabaran dan empati tim.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">I - Influence</span><h2 class="mt-3 text-lg font-bold text-slate-900">Persuasif, komunikatif, antusias</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Kuat dalam relasi, presentasi, dan engagement.</li><li>Cocok untuk peran sales, marketing, komunikasi.</li><li>Perlu menjaga konsistensi dan detail.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">S - Steadiness</span><h2 class="mt-3 text-lg font-bold text-slate-900">Stabil, suportif, konsisten</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Tenang, kooperatif, dan suka ritme kerja stabil.</li><li>Cocok untuk support, operasional, koordinasi.</li><li>Perlu adaptasi pada perubahan cepat.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">C - Compliance</span><h2 class="mt-3 text-lg font-bold text-slate-900">Teliti, sistematis, berbasis standar</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Kuat pada akurasi, kualitas, dan analisis.</li><li>Cocok untuk audit, QA, data, compliance.</li><li>Perlu melatih fleksibilitas dalam situasi ambigu.</li></ul></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-bold text-slate-900">Formula Hasil DISC</h2>
            <ul class="mt-3 list-disc pl-5 text-sm text-slate-700 space-y-1">
                <li>Setiap nomor: responden pilih <strong>Paling menggambarkan (P)</strong> dan <strong>Paling tidak menggambarkan (K)</strong>.</li>
                <li>Setiap pilihan P menambah <code>+1</code> pada tipe huruf pernyataan tersebut (D/I/S/C).</li>
                <li>Setiap pilihan K mengurangi <code>-1</code> pada tipe huruf pernyataan tersebut.</li>
                <li>Skor akhir: <code>D, I, S, C</code>. Tipe dominan adalah skor tertinggi.</li>
            </ul>
        </div>
    @elseif($type === 'MBTI')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">E vs I</span><h2 class="mt-3 text-lg font-bold text-slate-900">Sumber Energi</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li><strong>E (Extraversion)</strong>: energi dari interaksi eksternal.</li><li><strong>I (Introversion)</strong>: energi dari refleksi internal.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">S vs N</span><h2 class="mt-3 text-lg font-bold text-slate-900">Cara Memproses Informasi</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li><strong>S (Sensing)</strong>: fakta konkret, detail, realitas kini.</li><li><strong>N (Intuition)</strong>: pola, ide, makna, kemungkinan.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">T vs F</span><h2 class="mt-3 text-lg font-bold text-slate-900">Gaya Pengambilan Keputusan</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li><strong>T (Thinking)</strong>: logika, objektivitas, konsistensi aturan.</li><li><strong>F (Feeling)</strong>: nilai personal, empati, keharmonisan.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">J vs P</span><h2 class="mt-3 text-lg font-bold text-slate-900">Gaya Menjalankan Aktivitas</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li><strong>J (Judging)</strong>: terstruktur, terencana, closure cepat.</li><li><strong>P (Perceiving)</strong>: fleksibel, adaptif, opsi terbuka.</li></ul></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-bold text-slate-900">Formula Hasil MBTI</h2>
            <ul class="mt-3 list-disc pl-5 text-sm text-slate-700 space-y-1">
                <li>Setiap soal memiliki 2 opsi, masing-masing mewakili satu trait.</li>
                <li>Pilihan responden menambah <code>+1</code> pada trait terpilih.</li>
                <li>Total dihitung per pasangan: <code>E-I</code>, <code>S-N</code>, <code>T-F</code>, <code>J-P</code>.</li>
                <li>Kode tipe dibentuk dari skor tertinggi tiap pasangan, contoh: <code>ENTJ</code>, <code>ISFP</code>.</li>
                <li>Jika skor pasangan seri, sistem memakai huruf pertama pasangan sebagai tie-break: <code>E, S, T, J</code>.</li>
            </ul>
        </div>
    @elseif($type === 'OCEAN')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">O - Openness</span><h2 class="mt-3 text-lg font-bold text-slate-900">Keterbukaan terhadap pengalaman</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Tinggi: kreatif, eksploratif, terbuka ide baru.</li><li>Rendah: lebih nyaman pada pendekatan familiar dan praktis.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">C - Conscientiousness</span><h2 class="mt-3 text-lg font-bold text-slate-900">Kedisiplinan dan ketekunan</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Tinggi: terstruktur, teliti, tanggung jawab tinggi.</li><li>Rendah: cenderung spontan dan kurang konsisten.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">E - Extraversion</span><h2 class="mt-3 text-lg font-bold text-slate-900">Energi sosial</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Tinggi: ekspresif, aktif berinteraksi, percaya diri tampil.</li><li>Rendah: lebih reflektif, nyaman kerja fokus dan tenang.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">A - Agreeableness</span><h2 class="mt-3 text-lg font-bold text-slate-900">Kerja sama dan empati</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Tinggi: kooperatif, suportif, peduli relasi tim.</li><li>Rendah: lebih kompetitif, kritis, dan direct.</li></ul></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5"><span class="inline-flex rounded-full bg-brand-50 border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700">N - Neuroticism</span><h2 class="mt-3 text-lg font-bold text-slate-900">Stabilitas emosi</h2><ul class="mt-2 list-disc pl-5 text-sm text-slate-700 space-y-1"><li>Tinggi: lebih mudah cemas/tertekan di situasi berat.</li><li>Rendah: lebih stabil dan tenang di bawah tekanan.</li></ul></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-bold text-slate-900">Formula Hasil OCEAN (Big 5)</h2>
            <ul class="mt-3 list-disc pl-5 text-sm text-slate-700 space-y-1">
                <li>Setiap pernyataan dijawab dengan skala 1-5 (Sangat Tidak Sesuai sampai Sangat Sesuai).</li>
                <li>Untuk item normal, skor dihitung sesuai pilihan (1-5).</li>
                <li>Untuk item reverse, skor dibalik dengan rumus: <code>skor_akhir = 6 - skor_pilihan</code>.</li>
                <li>Skor trait dihitung dari total item dalam trait yang sama: <code>O, C, E, A, N</code>.</li>
                <li>Trait dominan adalah skor trait tertinggi.</li>
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="text-xl font-bold text-slate-900">Formula Rekomendasi Posisi (Semua Jenis Tes)</h2>
        <ul class="mt-3 list-disc pl-5 text-sm text-slate-700 space-y-1">
            <li>Setiap posisi memiliki <strong>profil target</strong> per jenis tes (DISC/MBTI/OCEAN).</li>
            <li>Sistem membandingkan vektor skor responden dengan vektor target posisi.</li>
            <li>Semakin kecil selisih total antar dimensi, semakin tinggi <strong>match score</strong>.</li>
            <li>Hasil diurutkan dari skor kecocokan tertinggi sebagai rekomendasi utama.</li>
        </ul>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="text-xl font-bold text-slate-900">Contoh Penggunaan Rekomendasi</h2>
        <ul class="mt-3 list-disc pl-5 text-sm text-slate-700 space-y-1">
            <li><strong>Screening awal rekrutmen:</strong> membantu shortlist kandidat lebih objektif.</li>
            <li><strong>Penempatan internal:</strong> memetakan kecocokan karyawan ke role tertentu.</li>
            <li><strong>Pengembangan tim:</strong> memahami komposisi perilaku dan gaya kerja tim.</li>
            <li>Keputusan final tetap dikombinasikan dengan wawancara, kompetensi teknis, dan budaya kerja.</li>
        </ul>
    </div>
</div>
@endsection
