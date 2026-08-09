@extends('layouts.app')

@section('title', 'Detail Orang Tua')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Detail Orang Tua" subtitle="{{ $orangTua->nama }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('orang-tua.index')">Kembali</x-button>
        @if ($isAdmin)
            <x-button variant="primary" :href="route('orang-tua.edit', $orangTua)"><x-icon name="edit" class="h-4 w-4" /> Edit</x-button>
        @endif
    </x-slot:actions>
</x-page-header>

<x-card>
    <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300">
            <x-icon name="orangtua" class="h-6 w-6" />
        </div>
        <div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $orangTua->nama }}</h3>
            <p class="text-sm text-slate-500">{{ $orangTua->hubungan ?? '-' }}</p>
        </div>
        @if ($orangTua->is_kontak_utama)
            <div class="ml-auto"><x-badge variant="brand">Kontak Utama</x-badge></div>
        @endif
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nama</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $orangTua->nama }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Hubungan</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $orangTua->hubungan ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Siswa</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $orangTua->siswa->nama_lengkap ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">No. HP</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $orangTua->no_hp ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">No. WhatsApp</dt>
            <dd class="mt-1 text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $orangTua->no_wa ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Pekerjaan</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $orangTua->pekerjaan ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Kontak Utama</dt>
            <dd class="mt-1 text-sm">
                @if ($orangTua->is_kontak_utama)
                    <x-badge variant="brand">Ya</x-badge>
                @else
                    <x-badge variant="slate">Tidak</x-badge>
                @endif
            </dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Alamat</dt>
            <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $orangTua->alamat ?? '-' }}</dd>
        </div>
    </dl>
</x-card>
@endsection
