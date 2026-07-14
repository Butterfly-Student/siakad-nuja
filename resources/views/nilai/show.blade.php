@extends('layouts.app')

@section('title', 'Detail Nilai')

@section('content')
<x-page-header title="Detail Nilai" subtitle="{{ $nilai->siswa->nama_lengkap ?? '-' }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('nilai.index')">Kembali</x-button>
        <x-button variant="primary" :href="route('nilai.edit', $nilai)"><x-icon name="edit" class="h-4 w-4" /> Edit</x-button>
    </x-slot:actions>
</x-page-header>

<x-card>
    <div class="mb-5 flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300">
            <x-icon name="nilai" class="h-5 w-5" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $nilai->mapel->nama_mapel ?? '-' }}</h3>
            <p class="text-sm text-slate-500">{{ $nilai->kelas->nama_kelas ?? '-' }} • {{ $nilai->semester }}</p>
        </div>
    </div>

    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Siswa</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $nilai->siswa->nama_lengkap ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Mata Pelajaran</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->mapel->nama_mapel ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Kelas</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->kelas->nama_kelas ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Semester</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->semester }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Tahun Ajaran</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->tahun_ajaran }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nilai Harian</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->nilai_harian ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nilai UTS</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->nilai_uts ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nilai UAS</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $nilai->nilai_uas ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nilai Akhir</dt>
            <dd class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ $nilai->nilai_akhir ?? '-' }}</dd></div>
        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Predikat</dt>
            <dd class="mt-1">
                @php $pColor = ['A' => 'success', 'B' => 'success', 'C' => 'info', 'D' => 'warning', 'E' => 'danger'][$nilai->predikat ?? ''] ?? 'slate'; @endphp
                <x-badge :variant="$pColor">{{ $nilai->predikat ?? '-' }}</x-badge>
            </dd></div>
    </dl>
</x-card>
@endsection
