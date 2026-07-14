@extends('layouts.app')

@section('title', 'Data Orang Tua')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<x-page-header title="Data Orang Tua" subtitle="Kelola data orang tua / wali siswa.">
    @if ($isAdmin)
        <x-slot:actions>
            <x-button :href="route('orang-tua.create')" variant="primary">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Orang Tua
            </x-button>
        </x-slot:actions>
    @endif
</x-page-header>

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
    <x-search-bar placeholder="Cari nama orang tua..." />
</div>

<x-card padding="p-0">
    @if ($orangTua->count())
        {{-- Desktop --}}
        <div class="hidden md:block">
            <x-table :headers="['Nama', 'Hubungan', 'Siswa', 'No. HP', 'Kontak Utama', 'Aksi']">
                @foreach ($orangTua as $ot)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">{{ $ot->nama }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $ot->hubungan ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $ot->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $ot->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($ot->is_kontak_utama)
                                <x-badge variant="brand">Utama</x-badge>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <x-button :href="route('orang-tua.show', $ot)" variant="ghost" size="icon" title="Lihat"><x-icon name="eye" class="h-4 w-4" /></x-button>
                                @if ($isAdmin)
                                    <x-button :href="route('orang-tua.edit', $ot)" variant="ghost" size="icon" title="Edit"><x-icon name="edit" class="h-4 w-4" /></x-button>
                                    <x-confirm-delete :action="route('orang-tua.destroy', $ot)" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
        {{-- Mobile --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach ($orangTua as $ot)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $ot->nama }}</p>
                            <p class="text-sm text-slate-500">{{ $ot->hubungan ?? '-' }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <x-button :href="route('orang-tua.show', $ot)" variant="ghost" size="icon"><x-icon name="eye" class="h-4 w-4" /></x-button>
                            @if ($isAdmin)
                                <x-button :href="route('orang-tua.edit', $ot)" variant="ghost" size="icon"><x-icon name="edit" class="h-4 w-4" /></x-button>
                                <x-confirm-delete :action="route('orang-tua.destroy', $ot)" />
                            @endif
                        </div>
                    </div>
                    <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                        <div><dt class="text-xs text-slate-400">Siswa</dt><dd class="text-slate-700 dark:text-slate-300">{{ $ot->siswa->nama_lengkap ?? '-' }}</dd></div>
                        <div><dt class="text-xs text-slate-400">No. HP</dt><dd class="text-slate-700 dark:text-slate-300">{{ $ot->no_hp ?? '-' }}</dd></div>
                        <div>
                            <dt class="text-xs text-slate-400">Kontak Utama</dt>
                            <dd>@if ($ot->is_kontak_utama) <x-badge variant="brand">Utama</x-badge> @else <span class="text-slate-400">-</span> @endif</dd>
                        </div>
                    </dl>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6"><x-empty-state icon="orangtua" title="Belum ada data orang tua" description="Data orang tua / wali akan muncul di sini." /></div>
    @endif
</x-card>

@if ($orangTua->hasPages())
    <div class="mt-4">{{ $orangTua->withQueryString()->links() }}</div>
@endif
@endsection
