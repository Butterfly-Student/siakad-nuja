@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Detail Jadwal" subtitle="{{ $jadwal->mapel->nama_mapel ?? '-' }} &middot; {{ $jadwal->kelas->nama_kelas ?? '-' }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('jadwal.index')">Kembali</x-button>
        @if ($isAdmin)
            <x-button variant="primary" :href="route('jadwal.edit', $jadwal)"><x-icon name="edit" class="h-4 w-4" /> Edit</x-button>
        @endif
    </x-slot:actions>
</x-page-header>

<x-card>
    <div class="mb-5 flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300">
            <x-icon name="jadwal" class="h-6 w-6" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $jadwal->mapel->nama_mapel ?? '-' }}</h3>
            <p class="text-sm text-slate-500">{{ $jadwal->hari }} &middot; Jam ke-{{ $jadwal->jam_ke }}</p>
        </div>
    </div>

    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 border-t border-slate-100 dark:border-slate-700 pt-5 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Hari</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->hari }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Jam Ke</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->jam_ke }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Waktu</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::substr($jadwal->jam_mulai, 0, 5) }}&ndash;{{ \Illuminate\Support\Str::substr($jadwal->jam_selesai, 0, 5) }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Mapel</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->mapel->nama_mapel ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Kelas</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->kelas->nama_kelas ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Guru</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->guru->nama_lengkap ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Ruangan</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->ruangan ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Tahun Ajaran</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $jadwal->tahun_ajaran }}</dd>
        </div>
    </dl>
</x-card>
@endsection
