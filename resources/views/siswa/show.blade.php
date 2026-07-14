@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Detail Siswa" subtitle="{{ $siswa->nama_lengkap }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('siswa.index')">Kembali</x-button>
        @if ($isAdmin)
            <x-button variant="primary" :href="route('siswa.edit', $siswa)"><x-icon name="edit" class="h-4 w-4" /> Edit</x-button>
        @endif
    </x-slot:actions>
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Identitas --}}
    <div class="lg:col-span-1">
        <x-card>
            <div class="flex flex-col items-center text-center">
                @if ($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto {{ $siswa->nama_lengkap }}"
                        class="h-24 w-24 rounded-full object-cover ring-2 ring-brand-100 dark:ring-brand-900">
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/40 text-2xl font-bold text-brand-600 dark:text-brand-300">
                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                    </div>
                @endif
                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">{{ $siswa->nama_lengkap }}</h3>
                <p class="text-sm text-slate-500">NIS {{ $siswa->nis }}</p>
                <div class="mt-3">
                    @php $stColor = ['Aktif' => 'success', 'Lulus' => 'info', 'Pindah' => 'warning', 'Keluar' => 'danger'][$siswa->status ?? 'Aktif'] ?? 'slate'; @endphp
                    <x-badge :variant="$stColor">{{ $siswa->status ?? 'Aktif' }}</x-badge>
                </div>
            </div>

            <dl class="mt-6 space-y-3 border-t border-slate-100 dark:border-slate-700 pt-4 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Kelas</dt><dd class="text-slate-900 dark:text-white font-medium">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Jenis Kelamin</dt><dd class="text-slate-700 dark:text-slate-300">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Tanggal Lahir</dt><dd class="text-slate-700 dark:text-slate-300">{{ optional($siswa->tanggal_lahir)->format('d M Y') ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Tahun Masuk</dt><dd class="text-slate-700 dark:text-slate-300">{{ $siswa->tahun_masuk }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Alamat</dt><dd class="text-right text-slate-700 dark:text-slate-300">{{ $siswa->alamat ?? '-' }}</dd></div>
            </dl>
        </x-card>
    </div>

    {{-- Relasi --}}
    <div class="space-y-6 lg:col-span-2">
        <x-card padding="p-0">
            <x-slot:header><h2 class="text-sm font-semibold text-slate-900 dark:text-white">Orang Tua / Wali</h2></x-slot:header>
            @if ($siswa->orangTua->isNotEmpty())
                <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @foreach ($siswa->orangTua as $ot)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 sm:px-6">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $ot->nama }}</p>
                                <p class="text-xs text-slate-500">{{ $ot->hubungan ?? '-' }} • {{ $ot->no_hp ?? '-' }}</p>
                            </div>
                            @if ($ot->is_kontak_utama) <x-badge variant="brand">Kontak Utama</x-badge> @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-6"><x-empty-state icon="orangtua" title="Belum ada data orang tua" /></div>
            @endif
        </x-card>

        <x-card padding="p-0">
            <x-slot:header><h2 class="text-sm font-semibold text-slate-900 dark:text-white">Rekap Nilai</h2></x-slot:header>
            @if ($siswa->nilai->isNotEmpty())
                <x-table :headers="['Mapel', 'Semester', 'Akhir', 'Predikat']">
                    @foreach ($siswa->nilai as $n)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $n->mapel->nama_mapel ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $n->semester }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $n->nilai_akhir ?? '-' }}</td>
                            <td class="px-4 py-3"><x-badge variant="brand">{{ $n->predikat ?? '-' }}</x-badge></td>
                        </tr>
                    @endforeach
                </x-table>
            @else
                <div class="p-6"><x-empty-state icon="nilai" title="Belum ada nilai" /></div>
            @endif
        </x-card>
    </div>
</div>
@endsection
