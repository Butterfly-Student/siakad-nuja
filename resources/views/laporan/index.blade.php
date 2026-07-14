@extends('layouts.app')

@section('title', 'Laporan Akademik')
@section('header', 'Laporan Akademik')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Rekap Kehadiran -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <x-icon name="absensi" class="h-6 w-6" />
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Rekap Kehadiran Kelas</h3>
                <p class="text-sm text-slate-500">Buat laporan jumlah kehadiran siswa per kelas dalam satu bulan.</p>
            </div>
        </div>
        <div class="p-6 bg-slate-50">
            <form action="{{ route('laporan.kehadiran') }}" method="GET" class="grid sm:grid-cols-3 gap-4 items-end" target="_blank">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full rounded-xl border-slate-200 py-3 px-4 focus:ring-2 focus:ring-brand-500" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Bulan</label>
                    <input type="month" name="bulan" value="{{ date('Y-m') }}" class="w-full rounded-xl border-slate-200 py-3 px-4 focus:ring-2 focus:ring-brand-500" required>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="export" value="" class="flex-1 bg-white border border-slate-200 text-slate-700 px-4 py-3 rounded-xl font-semibold hover:bg-slate-50 transition flex items-center justify-center gap-2">
                        Preview
                    </button>
                    <button type="submit" name="export" value="pdf" class="flex-1 bg-brand-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-brand-700 transition flex items-center justify-center gap-2 shadow-sm">
                        <x-icon name="download" class="h-5 w-5" /> PDF
                    </button>
                    <button type="submit" name="export" value="csv" class="flex-1 bg-emerald-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-sm">
                        <x-icon name="download" class="h-5 w-5" /> CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rekap Nilai -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                <x-icon name="nilai" class="h-6 w-6" />
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Rekap Nilai Kelas</h3>
                <p class="text-sm text-slate-500">Buat laporan nilai akhir siswa untuk mata pelajaran tertentu.</p>
            </div>
        </div>
        <div class="p-6 bg-slate-50">
            <form action="{{ route('laporan.nilai') }}" method="GET" class="grid sm:grid-cols-3 gap-4 items-end" target="_blank">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full rounded-xl border-slate-200 py-3 px-4 focus:ring-2 focus:ring-indigo-500" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" class="w-full rounded-xl border-slate-200 py-3 px-4 focus:ring-2 focus:ring-indigo-500" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="export" value="" class="flex-1 bg-white border border-slate-200 text-slate-700 px-4 py-3 rounded-xl font-semibold hover:bg-slate-50 transition flex items-center justify-center gap-2">
                        Preview
                    </button>
                    <button type="submit" name="export" value="pdf" class="flex-1 bg-indigo-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow-sm">
                        <x-icon name="download" class="h-5 w-5" /> PDF
                    </button>
                    <button type="submit" name="export" value="csv" class="flex-1 bg-emerald-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-sm">
                        <x-icon name="download" class="h-5 w-5" /> CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
