@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

<x-page-header
    title="Halo, {{ $user->nama }} 👋"
    subtitle="{{ $role === 'admin' ? 'Ringkasan data akademik sekolah.' : 'Ringkasan kelas & jadwal mengajar Anda.' }}" />

@if ($role === 'admin')
    {{-- ===== Dashboard Admin ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Siswa" :value="$stats['total_siswa']" icon="siswa" color="brand" :href="route('siswa.index')" />
        <x-stat-card label="Total Guru" :value="$stats['total_guru']" icon="guru" color="emerald" :href="route('guru.index')" />
        <x-stat-card label="Total Kelas" :value="$stats['total_kelas']" icon="kelas" color="amber" :href="route('kelas.index')" />
        <x-stat-card label="Mata Pelajaran" :value="$stats['total_mapel']" icon="mapel" color="sky" :href="route('mata-pelajaran.index')" />
    </div>
@else
    {{-- ===== Dashboard Guru ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Jadwal" :value="$stats['total_jadwal']" icon="jadwal" color="brand" />
        <x-stat-card label="Kelas Diampu" :value="$stats['kelas_diampu']" icon="kelas" color="emerald" />
        <x-stat-card label="Jadwal Hari Ini" :value="$stats['jadwal_hari_ini']" icon="clock" color="amber" />
        <x-stat-card label="Kelas Wali" :value="$stats['kelas_wali']" icon="orangtua" color="sky" />
    </div>
@endif

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Kolom utama --}}
    <div class="space-y-6 lg:col-span-2">
        @if ($role === 'guru')
            <x-card padding="p-0">
                <x-slot:header>
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="h-5 w-5 text-brand-500" />
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Jadwal Mengajar Hari Ini — {{ $hariIni }}</h2>
                    </div>
                </x-slot:header>

                @if ($jadwalHariIni->isNotEmpty())
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach ($jadwalHariIni as $j)
                            <li class="flex items-center gap-4 px-5 py-3 sm:px-6">
                                <div class="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300">
                                    <span class="text-[10px] uppercase leading-none">Jam</span>
                                    <span class="text-sm font-bold leading-none">{{ $j->jam_ke }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $j->mapel->nama_mapel ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $j->kelas->nama_kelas ?? '-' }} • {{ \Illuminate\Support\Str::of($j->jam_mulai)->substr(0,5) }}–{{ \Illuminate\Support\Str::of($j->jam_selesai)->substr(0,5) }} • {{ $j->ruangan ?? '-' }}</p>
                                </div>
                                <x-button :href="route('absensi.create')" variant="secondary" size="sm">Absensi</x-button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-6"><x-empty-state icon="jadwal" title="Tidak ada jadwal hari ini" description="Nikmati harimu!" /></div>
                @endif
            </x-card>
        @endif

        <x-card padding="p-0">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <x-icon name="pengumuman" class="h-5 w-5 text-brand-500" />
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Pengumuman Terbaru</h2>
                </div>
            </x-slot:header>

            @if ($pengumumanTerbaru->isNotEmpty())
                <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @foreach ($pengumumanTerbaru as $item)
                        <li class="px-5 py-4 sm:px-6">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item->judul }}</h3>
                                <span class="shrink-0 text-xs text-slate-400">{{ $item->tanggal_publish?->format('d M Y') ?? $item->created_at?->format('d M Y') }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($item->konten, 140) }}</p>
                            <p class="mt-1 text-xs text-slate-400">— {{ $item->pembuat->nama ?? 'Admin' }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-6"><x-empty-state icon="pengumuman" title="Belum ada pengumuman" /></div>
            @endif
        </x-card>
    </div>

    {{-- Kolom samping --}}
    <div class="space-y-6">
        @if ($role === 'guru' && $kelasWali->isNotEmpty())
            <x-card padding="p-0">
                <x-slot:header>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Kelas yang Anda Walikan</h2>
                </x-slot:header>
                <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @foreach ($kelasWali as $k)
                        <li class="flex items-center justify-between px-5 py-3 sm:px-6">
                            <div class="flex items-center gap-3">
                                <x-icon name="kelas" class="h-5 w-5 text-slate-400" />
                                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $k->nama_kelas }}</span>
                            </div>
                            <x-badge variant="brand">{{ $k->siswa_count }} siswa</x-badge>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endif

        <x-card>
            <x-slot:header>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Pintasan</h2>
            </x-slot:header>
            <div class="grid grid-cols-2 gap-3">
                <x-button :href="route('nilai.create')" variant="secondary" class="flex-col py-4!">
                    <x-icon name="nilai" class="h-5 w-5" /> Input Nilai
                </x-button>
                <x-button :href="route('absensi.create')" variant="secondary" class="flex-col py-4!">
                    <x-icon name="absensi" class="h-5 w-5" /> Absensi
                </x-button>
                @if ($role === 'admin')
                    <x-button :href="route('siswa.create')" variant="secondary" class="flex-col py-4!">
                        <x-icon name="siswa" class="h-5 w-5" /> Tambah Siswa
                    </x-button>
                    <x-button :href="route('pengumuman.create')" variant="secondary" class="flex-col py-4!">
                        <x-icon name="pengumuman" class="h-5 w-5" /> Pengumuman
                    </x-button>
                @endif
            </div>
        </x-card>
    </div>
</div>
@endsection
