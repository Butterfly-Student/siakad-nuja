@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
@php
    $badge = ['Hadir' => 'success', 'Izin' => 'info', 'Sakit' => 'warning', 'Alpa' => 'danger'];
@endphp

<x-page-header title="Detail Absensi" subtitle="{{ $absensi->siswa->nama_lengkap ?? '-' }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('absensi.index')">Kembali</x-button>
    </x-slot:actions>
</x-page-header>

<x-card>
    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Siswa</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $absensi->siswa->nama_lengkap ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Tanggal</dt>
            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ optional($absensi->tanggal)->translatedFormat('l, d F Y') }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Mata Pelajaran</dt>
            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $absensi->jadwal->mapel->nama_mapel ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Kelas</dt>
            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $absensi->jadwal->kelas->nama_kelas ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Status</dt>
            <dd class="mt-1"><x-badge :variant="$badge[$absensi->status] ?? 'slate'">{{ $absensi->status ?? '-' }}</x-badge></dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Keterangan</dt>
            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $absensi->keterangan ?? '-' }}</dd>
        </div>
    </dl>
</x-card>
@endsection
