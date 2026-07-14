@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<x-page-header title="Data Absensi" subtitle="Rekap kehadiran siswa per jadwal & tanggal.">
    <x-slot:actions>
        <x-button :href="route('absensi.create')" variant="primary">
            <x-icon name="plus" class="h-4 w-4" /> Entri Absensi
        </x-button>
    </x-slot:actions>
</x-page-header>

<x-card padding="p-0">
    @if ($absensi->count())
        @php
            $badge = ['Hadir' => 'success', 'Izin' => 'info', 'Sakit' => 'warning', 'Alpa' => 'danger'];
        @endphp
        {{-- Desktop --}}
        <div class="hidden md:block">
            <x-table :headers="['Tanggal', 'Siswa', 'Mapel', 'Kelas', 'Status', 'Keterangan', 'Aksi']">
                @foreach ($absensi as $a)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ optional($a->tanggal)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">{{ $a->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $a->jadwal->mapel->nama_mapel ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $a->jadwal->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><x-badge :variant="$badge[$a->status] ?? 'slate'">{{ $a->status ?? '-' }}</x-badge></td>
                        <td class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $a->keterangan ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <x-button :href="route('absensi.show', $a)" variant="ghost" size="icon" title="Lihat"><x-icon name="eye" class="h-4 w-4" /></x-button>
                                <x-confirm-delete :action="route('absensi.destroy', $a)" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
        {{-- Mobile --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach ($absensi as $a)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $a->siswa->nama_lengkap ?? '-' }}</p>
                            <p class="text-sm text-slate-500">{{ optional($a->tanggal)->format('d M Y') }} • {{ $a->jadwal->mapel->nama_mapel ?? '-' }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <x-button :href="route('absensi.show', $a)" variant="ghost" size="icon"><x-icon name="eye" class="h-4 w-4" /></x-button>
                            <x-confirm-delete :action="route('absensi.destroy', $a)" />
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <x-badge :variant="$badge[$a->status] ?? 'slate'">{{ $a->status ?? '-' }}</x-badge>
                        @if ($a->keterangan)<span class="text-xs text-slate-500">{{ $a->keterangan }}</span>@endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6"><x-empty-state icon="absensi" title="Belum ada data absensi" description="Mulai dengan entri absensi baru." /></div>
    @endif
</x-card>

@if ($absensi->hasPages())
    <div class="mt-4">{{ $absensi->links() }}</div>
@endif
@endsection
