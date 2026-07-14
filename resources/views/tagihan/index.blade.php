@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran')

@section('content')

<x-page-header title="Tagihan & Pembayaran" subtitle="Kelola tagihan SPP dan verifikasi pembayaran siswa.">
    <x-slot:actions>
        <x-button :href="route('tagihan.create')" variant="primary">
            <x-icon name="plus" class="h-4 w-4" /> Buat Tagihan
        </x-button>
    </x-slot:actions>
</x-page-header>

{{-- 4 Kartu Ringkasan --}}
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                <x-icon name="tagihan" class="h-6 w-6" />
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Tagihan</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($summary['total']) }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-emerald-200/80 bg-white p-5 shadow-sm dark:border-emerald-800/50 dark:bg-slate-800">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                <x-icon name="check" class="h-6 w-6" />
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Lunas</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($summary['lunas']) }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-amber-200/80 bg-white p-5 shadow-sm dark:border-amber-800/50 dark:bg-slate-800">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300">
                <x-icon name="clock" class="h-6 w-6" />
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Menunggu Verifikasi</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($summary['menunggu']) }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-rose-200/80 bg-white p-5 shadow-sm dark:border-rose-800/50 dark:bg-slate-800">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300">
                <x-icon name="warning" class="h-6 w-6" />
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tunggakan</p>
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ number_format($summary['tunggakan']) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
    <x-search-bar placeholder="Cari nama siswa..." class="flex-1" />
    <select name="kelas_id" onchange="this.form.submit()"
        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white sm:w-48">
        <option value="">Semua Kelas</option>
        @foreach ($kelasList as $k)
            <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama_kelas }}</option>
        @endforeach
    </select>
    <select name="status" onchange="this.form.submit()"
        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white sm:w-52">
        <option value="">Semua Status</option>
        <option value="belum_dibayar" @selected(request('status') === 'belum_dibayar')>Belum Dibayar</option>
        <option value="menunggu_verifikasi" @selected(request('status') === 'menunggu_verifikasi')>Menunggu Verifikasi</option>
        <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
    </select>
</form>

<x-card padding="p-0">
    @if ($tagihan->count())
        {{-- Desktop Table --}}
        <div class="hidden md:block">
            <x-table :headers="['Siswa', 'Kelas', 'Judul Tagihan', 'Periode', 'Nominal', 'Jatuh Tempo', 'Status', 'Aksi']">
                @foreach ($tagihan as $t)
                    @php
                        $statusColor = match($t->status) {
                            'lunas'               => 'success',
                            'menunggu_verifikasi' => 'warning',
                            default               => 'danger',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-5 py-3.5 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $t->siswa->nama_lengkap ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            {{ $t->siswa->kelas->nama_kelas ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 dark:text-slate-300">{{ $t->judul }}</td>
                        <td class="px-5 py-3.5 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $t->periode }}</td>
                        <td class="px-5 py-3.5 text-sm font-semibold text-slate-800 dark:text-white whitespace-nowrap">
                            Rp {{ number_format((float)$t->nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            @if ($t->jatuh_tempo)
                                <span class="{{ $t->isTunggakan() ? 'text-rose-500 font-semibold' : '' }}">
                                    {{ $t->jatuh_tempo->format('d M Y') }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <x-badge :variant="$statusColor">{{ $t->statusLabel() }}</x-badge>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <x-button :href="route('tagihan.show', $t)" variant="ghost" size="icon" title="Detail">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </x-button>
                                <x-button :href="route('tagihan.edit', $t)" variant="ghost" size="icon" title="Edit">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </x-button>
                                <x-confirm-delete :action="route('tagihan.destroy', $t)" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        {{-- Mobile Cards --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach ($tagihan as $t)
                @php
                    $statusColor = match($t->status) {
                        'lunas'               => 'success',
                        'menunggu_verifikasi' => 'warning',
                        default               => 'danger',
                    };
                @endphp
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $t->siswa->nama_lengkap ?? '-' }}</p>
                            <p class="text-sm text-slate-500">{{ $t->judul }} · {{ $t->periode }}</p>
                        </div>
                        <x-badge :variant="$statusColor">{{ $t->statusLabel() }}</x-badge>
                    </div>
                    <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                        <div><dt class="text-xs text-slate-400">Nominal</dt><dd class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format((float)$t->nominal, 0, ',', '.') }}</dd></div>
                        <div><dt class="text-xs text-slate-400">Kelas</dt><dd class="text-slate-700 dark:text-slate-300">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</dd></div>
                    </dl>
                    <div class="mt-3 flex items-center gap-2">
                        <x-button :href="route('tagihan.show', $t)" variant="ghost" size="sm">Detail</x-button>
                        <x-button :href="route('tagihan.edit', $t)" variant="ghost" size="sm">Edit</x-button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-8">
            <x-empty-state icon="tagihan" title="Belum ada tagihan" description="Buat tagihan baru untuk siswa atau kelas tertentu." />
        </div>
    @endif
</x-card>

@if ($tagihan->hasPages())
    <div class="mt-4">{{ $tagihan->links() }}</div>
@endif

@endsection
