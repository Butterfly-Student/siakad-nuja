@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Data Guru" subtitle="Kelola data guru dan akunnya.">
    @if ($isAdmin)
        <x-slot:actions>
            <x-button :href="route('guru.create')" variant="primary">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Guru
            </x-button>
        </x-slot:actions>
    @endif
</x-page-header>

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
    <x-search-bar placeholder="Cari nama atau NIP..." />
</div>

<x-card padding="p-0">
    @if ($guru->count())
        {{-- Desktop --}}
        <div class="hidden md:block">
            <x-table :headers="['NIP', 'Nama', 'Jabatan', 'No. HP', 'Aksi']">
                @foreach ($guru as $g)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $g->nip }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">{{ $g->nama_lengkap }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $g->jabatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $g->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <x-button :href="route('guru.show', $g)" variant="ghost" size="icon" title="Lihat"><x-icon name="eye" class="h-4 w-4" /></x-button>
                                @if ($isAdmin)
                                    <x-button :href="route('guru.edit', $g)" variant="ghost" size="icon" title="Edit"><x-icon name="edit" class="h-4 w-4" /></x-button>
                                    <x-confirm-delete :action="route('guru.destroy', $g)" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
        {{-- Mobile --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach ($guru as $g)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $g->nama_lengkap }}</p>
                            <p class="text-sm text-slate-500">NIP {{ $g->nip }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <x-button :href="route('guru.show', $g)" variant="ghost" size="icon"><x-icon name="eye" class="h-4 w-4" /></x-button>
                            @if ($isAdmin)
                                <x-button :href="route('guru.edit', $g)" variant="ghost" size="icon"><x-icon name="edit" class="h-4 w-4" /></x-button>
                                <x-confirm-delete :action="route('guru.destroy', $g)" />
                            @endif
                        </div>
                    </div>
                    <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                        <div><dt class="text-xs text-slate-400">Jabatan</dt><dd class="text-slate-700 dark:text-slate-300">{{ $g->jabatan ?? '-' }}</dd></div>
                        <div><dt class="text-xs text-slate-400">No. HP</dt><dd class="text-slate-700 dark:text-slate-300">{{ $g->no_hp ?? '-' }}</dd></div>
                        <div class="col-span-2"><dt class="text-xs text-slate-400">Email</dt><dd class="text-slate-700 dark:text-slate-300 truncate">{{ $g->user->email ?? '-' }}</dd></div>
                    </dl>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6"><x-empty-state icon="guru" title="Belum ada guru" description="Data guru akan muncul di sini." /></div>
    @endif
</x-card>

@if ($guru->hasPages())
    <div class="mt-4">{{ $guru->withQueryString()->links() }}</div>
@endif
@endsection
