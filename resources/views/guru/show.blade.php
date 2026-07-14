@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Detail Guru" subtitle="{{ $guru->nama_lengkap }}">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('guru.index')">Kembali</x-button>
        @if ($isAdmin)
            <x-button variant="primary" :href="route('guru.edit', $guru)"><x-icon name="edit" class="h-4 w-4" /> Edit</x-button>
        @endif
    </x-slot:actions>
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Identitas --}}
    <div class="lg:col-span-1">
        <x-card>
            <div class="flex flex-col items-center text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300">
                    <x-icon name="guru" class="h-10 w-10" />
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">{{ $guru->nama_lengkap }}</h3>
                <p class="text-sm text-slate-500">NIP {{ $guru->nip }}</p>
            </div>

            <dl class="mt-6 space-y-3 border-t border-slate-100 dark:border-slate-700 pt-4 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Jabatan</dt><dd class="text-slate-900 dark:text-white font-medium">{{ $guru->jabatan ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-400">No. HP</dt><dd class="text-slate-700 dark:text-slate-300">{{ $guru->no_hp ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-400">Email</dt><dd class="text-right text-slate-700 dark:text-slate-300">{{ $guru->user->email ?? '-' }}</dd></div>
            </dl>
        </x-card>
    </div>

    {{-- Relasi --}}
    <div class="space-y-6 lg:col-span-2">
        <x-card padding="p-0">
            <x-slot:header><h2 class="text-sm font-semibold text-slate-900 dark:text-white">Wali Kelas</h2></x-slot:header>
            @if ($guru->kelasWali->isNotEmpty())
                <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @foreach ($guru->kelasWali as $kelas)
                        <li class="flex items-center gap-3 px-5 py-3 sm:px-6">
                            <x-icon name="kelas" class="h-4 w-4 text-slate-400" />
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $kelas->nama_kelas }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-6"><x-empty-state icon="kelas" title="Belum menjadi wali kelas" /></div>
            @endif
        </x-card>

        <x-card padding="p-0">
            <x-slot:header><h2 class="text-sm font-semibold text-slate-900 dark:text-white">Jadwal Mengajar</h2></x-slot:header>
            @if ($guru->jadwal->isNotEmpty())
                <x-table :headers="['Hari', 'Mata Pelajaran', 'Kelas', 'Jam Ke']">
                    @foreach ($guru->jadwal as $j)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">{{ $j->hari }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $j->mapel->nama_mapel ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $j->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $j->jam_ke ?? '-' }}</td>
                        </tr>
                    @endforeach
                </x-table>
            @else
                <div class="p-6"><x-empty-state icon="jadwal" title="Belum ada jadwal mengajar" /></div>
            @endif
        </x-card>
    </div>
</div>
@endsection
