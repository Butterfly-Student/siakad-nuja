@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')

<x-page-header title="Detail Tagihan" subtitle="Informasi tagihan dan riwayat pembayaran.">
    <x-slot:actions>
        <x-button :href="route('tagihan.edit', $tagihan)" variant="secondary">
            <x-icon name="edit" class="h-4 w-4" /> Edit
        </x-button>
        <x-button :href="route('tagihan.index')" variant="ghost">← Kembali</x-button>
    </x-slot:actions>
</x-page-header>

<div class="grid gap-6 lg:grid-cols-3">

    {{-- Kolom kiri: Detail tagihan --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Info Siswa --}}
        <x-card>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Informasi Siswa</h3>
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300 text-lg font-bold">
                    {{ strtoupper(substr($tagihan->siswa->nama_lengkap ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $tagihan->siswa->nama_lengkap ?? '-' }}</p>
                    <p class="text-sm text-slate-500">{{ $tagihan->siswa->kelas->nama_kelas ?? '-' }} · NIS {{ $tagihan->siswa->nis ?? '-' }}</p>
                </div>
            </div>
        </x-card>

        {{-- Info Tagihan --}}
        <x-card>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4">Detail Tagihan</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500 dark:text-slate-400">Judul</dt>
                    <dd class="font-medium text-slate-800 dark:text-white text-right">{{ $tagihan->judul }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500 dark:text-slate-400">Jenis</dt>
                    <dd class="font-medium text-slate-800 dark:text-white">{{ $tagihan->jenis }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500 dark:text-slate-400">Periode</dt>
                    <dd class="font-medium text-slate-800 dark:text-white">{{ $tagihan->periode }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500 dark:text-slate-400">Nominal</dt>
                    <dd class="text-lg font-bold text-slate-900 dark:text-white">Rp {{ number_format((float)$tagihan->nominal, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500 dark:text-slate-400">Jatuh Tempo</dt>
                    <dd class="{{ $tagihan->isTunggakan() ? 'text-rose-500 font-semibold' : 'text-slate-800 dark:text-white' }}">
                        {{ $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d M Y') : '—' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                    <dt class="text-slate-500 dark:text-slate-400">Status</dt>
                    <dd>
                        @php
                            $sc = match($tagihan->status) {
                                'lunas'               => 'success',
                                'menunggu_verifikasi' => 'warning',
                                default               => 'danger',
                            };
                        @endphp
                        <x-badge :variant="$sc">{{ $tagihan->statusLabel() }}</x-badge>
                    </dd>
                </div>
            </dl>
            @if ($tagihan->keterangan)
                <div class="mt-4 rounded-xl bg-slate-50 p-3 text-sm text-slate-600 dark:bg-slate-800/60 dark:text-slate-400">
                    {{ $tagihan->keterangan }}
                </div>
            @endif
        </x-card>

    </div>

    {{-- Kolom kanan: Riwayat Pembayaran --}}
    <div class="lg:col-span-2 space-y-4">

        <x-card>
            <h3 class="mb-5 text-sm font-semibold uppercase tracking-wider text-slate-400">Riwayat Pembayaran</h3>

            @if ($tagihan->pembayaran->count())
                <div class="space-y-4">
                    @foreach ($tagihan->pembayaran->sortByDesc('created_at') as $p)
                        @php
                            $pc = match($p->status) {
                                'disetujui' => ['color' => 'emerald', 'label' => 'Disetujui'],
                                'ditolak'   => ['color' => 'rose',    'label' => 'Ditolak'],
                                default     => ['color' => 'amber',   'label' => 'Menunggu'],
                            };
                        @endphp
                        <div x-data="{ openVerif: false, openTolak: false }"
                             class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">
                                        Rp {{ number_format((float)$p->nominal, 0, ',', '.') }}
                                        <span class="ml-2 text-sm font-normal text-slate-500">via {{ $p->bank ?? $p->metode ?? '-' }}</span>
                                    </p>
                                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                                        a.n. {{ $p->nama_pengirim ?? '-' }} · {{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d M Y') : '-' }}
                                    </p>
                                </div>
                                <x-badge :variant="$pc['color']">{{ $pc['label'] }}</x-badge>
                            </div>

                            {{-- Bukti Transfer --}}
                            @if ($p->bukti)
                                <div class="mt-3">
                                    <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">
                                        <x-icon name="eye" class="h-3.5 w-3.5" /> Lihat Bukti Transfer
                                    </a>
                                </div>
                            @endif

                            {{-- Catatan verifikator --}}
                            @if ($p->catatan)
                                <p class="mt-2 text-xs text-slate-500">Catatan: {{ $p->catatan }}</p>
                            @endif
                            @if ($p->alasan_tolak)
                                <p class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600 dark:bg-rose-900/20 dark:text-rose-400">
                                    <strong>Alasan ditolak:</strong> {{ $p->alasan_tolak }}
                                </p>
                            @endif
                            @if ($p->diverifikasi_oleh)
                                <p class="mt-1.5 text-xs text-slate-400">
                                    Diverifikasi oleh: {{ $p->verifikator->nama ?? '-' }}
                                    · {{ $p->diverifikasi_pada?->format('d M Y H:i') }}
                                </p>
                            @endif

                            {{-- Tombol Aksi (hanya jika menunggu) --}}
                            @if ($p->status === 'menunggu')
                                <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4 dark:border-slate-700">

                                    {{-- Verifikasi --}}
                                    <button @click="openVerif = true" type="button"
                                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                                        <x-icon name="check" class="h-4 w-4" /> Verifikasi
                                    </button>

                                    {{-- Tolak --}}
                                    <button @click="openTolak = true" type="button"
                                        class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition">
                                        <x-icon name="x-circle" class="h-4 w-4" /> Tolak
                                    </button>

                                    {{-- Modal Verifikasi --}}
                                    <div x-show="openVerif" x-cloak @click.self="openVerif = false"
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4" style="display:none">
                                        <div @click.stop class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
                                            <h4 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">Konfirmasi Verifikasi</h4>
                                            <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">
                                                Verifikasi pembayaran <strong>Rp {{ number_format((float)$p->nominal, 0, ',', '.') }}</strong>
                                                dari <strong>{{ $p->nama_pengirim }}</strong>?
                                                Tagihan akan ditandai <span class="text-emerald-600 font-semibold">Lunas</span>.
                                            </p>
                                            <form method="POST" action="{{ route('tagihan.verifikasi', $p) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                                        Catatan (opsional)
                                                    </label>
                                                    <textarea name="catatan" rows="3"
                                                        class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                                                        placeholder="Tambahkan catatan verifikasi..."></textarea>
                                                </div>
                                                <div class="flex items-center justify-end gap-3">
                                                    <button type="button" @click="openVerif = false"
                                                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                                        Ya, Verifikasi
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Modal Tolak --}}
                                    <div x-show="openTolak" x-cloak @click.self="openTolak = false"
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4" style="display:none">
                                        <div @click.stop class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
                                            <h4 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">Tolak Pembayaran</h4>
                                            <form method="POST" action="{{ route('tagihan.tolak', $p) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                                        Alasan Penolakan <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea name="alasan_tolak" rows="3" required
                                                        class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                                                        placeholder="Jelaskan alasan penolakan..."></textarea>
                                                </div>
                                                <div class="flex items-center justify-end gap-3">
                                                    <button type="button" @click="openTolak = false"
                                                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="rounded-xl bg-rose-600 px-5 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                                                        Tolak Pembayaran
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state icon="tagihan" title="Belum ada pembayaran"
                    description="Siswa belum mengupload bukti pembayaran untuk tagihan ini." />
            @endif
        </x-card>

    </div>
</div>

@endsection
