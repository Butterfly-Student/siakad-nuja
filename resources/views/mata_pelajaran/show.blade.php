@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Detail Mata Pelajaran" subtitle="{{ $mapel->nama_mapel }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('mata-pelajaran.index')">Kembali</x-button>
        @if ($isAdmin)
            <x-button variant="primary" :href="route('mata-pelajaran.edit', $mapel)"><x-icon name="edit" class="h-4 w-4" /> Edit</x-button>
        @endif
    </x-slot:actions>
</x-page-header>

<x-card>
    <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300">
            <x-icon name="mapel" class="h-5 w-5" />
        </div>
        <div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $mapel->nama_mapel }}</h3>
            <p class="text-sm text-slate-500">{{ $mapel->kode_mapel }}</p>
        </div>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 border-t border-slate-100 dark:border-slate-700 pt-6 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Kode</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $mapel->kode_mapel }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nama Mapel</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $mapel->nama_mapel }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Jenjang</dt>
            <dd class="mt-1"><x-badge variant="brand">{{ $mapel->jenjang }}</x-badge></dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">KKM</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $mapel->kkm ?? '-' }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Deskripsi</dt>
            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $mapel->deskripsi ?? '-' }}</dd>
        </div>
    </dl>
</x-card>
@endsection
