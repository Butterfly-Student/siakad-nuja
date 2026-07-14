@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Mata Pelajaran" subtitle="Kelola daftar mata pelajaran dan KKM.">
    @if ($isAdmin)
        <x-slot:actions>
            <x-button :href="route('mata-pelajaran.create')" variant="primary">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Mapel
            </x-button>
        </x-slot:actions>
    @endif
</x-page-header>

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
    <x-search-bar placeholder="Cari mapel atau kode..." />
</div>

<x-card padding="p-0">
    @if ($mapel->count())
        {{-- Desktop --}}
        <div class="hidden md:block">
            <x-table :headers="['Kode', 'Nama Mapel', 'Jenjang', 'KKM', 'Aksi']">
                @foreach ($mapel as $m)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $m->kode_mapel }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">{{ $m->nama_mapel }}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><x-badge variant="brand">{{ $m->jenjang }}</x-badge></td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $m->kkm ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <x-button :href="route('mata-pelajaran.show', $m)" variant="ghost" size="icon" title="Lihat"><x-icon name="eye" class="h-4 w-4" /></x-button>
                                @if ($isAdmin)
                                    <x-button :href="route('mata-pelajaran.edit', $m)" variant="ghost" size="icon" title="Edit"><x-icon name="edit" class="h-4 w-4" /></x-button>
                                    <x-confirm-delete :action="route('mata-pelajaran.destroy', $m)" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
        {{-- Mobile --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach ($mapel as $m)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $m->nama_mapel }}</p>
                            <p class="text-sm text-slate-500">{{ $m->kode_mapel }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <x-button :href="route('mata-pelajaran.show', $m)" variant="ghost" size="icon"><x-icon name="eye" class="h-4 w-4" /></x-button>
                            @if ($isAdmin)
                                <x-button :href="route('mata-pelajaran.edit', $m)" variant="ghost" size="icon"><x-icon name="edit" class="h-4 w-4" /></x-button>
                                <x-confirm-delete :action="route('mata-pelajaran.destroy', $m)" />
                            @endif
                        </div>
                    </div>
                    <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                        <div><dt class="text-xs text-slate-400">Jenjang</dt><dd class="text-slate-700 dark:text-slate-300">{{ $m->jenjang }}</dd></div>
                        <div><dt class="text-xs text-slate-400">KKM</dt><dd class="text-slate-700 dark:text-slate-300">{{ $m->kkm ?? '-' }}</dd></div>
                    </dl>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6"><x-empty-state icon="mapel" title="Belum ada mata pelajaran" description="Data mata pelajaran akan muncul di sini." /></div>
    @endif
</x-card>

@if ($mapel->hasPages())
    <div class="mt-4">{{ $mapel->withQueryString()->links() }}</div>
@endif
@endsection
