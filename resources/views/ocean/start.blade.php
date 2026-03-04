@extends('layouts.tw')

@section('title', 'Tes Kepribadian OCEAN')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-7">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Tes Kepribadian OCEAN (Big 5)</h1>
        <p class="mt-2 text-slate-600">Sesi: <strong>{{ $session->name }}</strong> (Kode: {{ $session->code }})</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/ocean/start" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <input type="hidden" name="access_code" value="{{ $session->code }}">

            <div class="md:col-span-2"><label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap *</label><input name="nama" value="{{ old('nama') }}" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Institusi/Perusahaan *</label><input name="institusi_perusahaan" value="{{ old('institusi_perusahaan') }}" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Departemen/Divisi *</label><input name="departemen_divisi" value="{{ old('departemen_divisi') }}" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Jabatan Saat Ini</label><input name="jabatan_saat_ini" value="{{ old('jabatan_saat_ini') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Usia *</label><input type="number" min="15" max="80" name="usia" value="{{ old('usia') }}" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin *</label>
                <select name="jenis_kelamin" required class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400">
                    <option value="">Pilih...</option>
                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
            </div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Pendidikan Terakhir</label><input name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Pengalaman Kerja (tahun)</label><input type="number" min="0" max="60" name="lama_pengalaman_kerja" value="{{ old('lama_pengalaman_kerja') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Lokasi/Kota</label><input name="lokasi_kota" value="{{ old('lokasi_kota') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tujuan Tes</label>
                <select name="tujuan_tes" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400">
                    <option value="">Pilih...</option>
                    <option value="Rekrutmen" @selected(old('tujuan_tes') === 'Rekrutmen')>Rekrutmen</option>
                    <option value="Promosi" @selected(old('tujuan_tes') === 'Promosi')>Promosi</option>
                    <option value="Pengembangan Tim" @selected(old('tujuan_tes') === 'Pengembangan Tim')>Pengembangan Tim</option>
                </select>
            </div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Email (opsional)</label><input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>
            <div class="md:col-span-2"><label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor HP (opsional)</label><input name="nomor_hp" value="{{ old('nomor_hp') }}" class="w-full rounded-xl border-slate-300 px-4 py-3 focus:border-brand-400 focus:ring-brand-400"></div>

            <div class="md:col-span-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">Durasi tes maksimal 10 menit. Pilih jawaban paling sesuai dengan kebiasaan Anda sehari-hari.</div>
            <div class="md:col-span-2"><button class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-6 py-3 text-white font-bold hover:bg-brand-600" type="submit">Mulai Tes OCEAN</button></div>
        </form>
    </div>
</div>
@endsection
