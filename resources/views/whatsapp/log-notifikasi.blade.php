@extends('layouts.app')
@section('title', 'Log Notifikasi WhatsApp')
@section('header', 'Log Notifikasi WhatsApp')

@section('content')
<div class="space-y-4">
    <x-alert />

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <form method="GET" class="flex items-center gap-4 flex-wrap">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="rounded-xl border-slate-200 py-2 px-3 text-sm focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua</option>
                    <option value="terkirim" @selected(request('status')=='terkirim')>Terkirim</option>
                    <option value="gagal" @selected(request('status')=='gagal')>Gagal</option>
                    <option value="pending" @selected(request('status')=='pending')>Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis</label>
                <select name="jenis" class="rounded-xl border-slate-200 py-2 px-3 text-sm focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua</option>
                    <option value="absensi" @selected(request('jenis')=='absensi')>Absensi</option>
                    <option value="nilai" @selected(request('jenis')=='nilai')>Nilai</option>
                    <option value="tagihan" @selected(request('jenis')=='tagihan')>Tagihan</option>
                    <option value="pengumuman" @selected(request('jenis')=='pengumuman')>Pengumuman</option>
                </select>
            </div>
            <div class="self-end flex gap-2">
                <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
                <a href="{{ route('whatsapp.log-notifikasi') }}" class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-semibold">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 font-semibold">Wali / Nomor</th>
                    <th class="px-4 py-3 font-semibold">Siswa</th>
                    <th class="px-4 py-3 font-semibold">Jenis</th>
                    <th class="px-4 py-3 font-semibold">Isi Pesan</th>
                    <th class="px-4 py-3 font-semibold text-center">Status</th>
                    <th class="px-4 py-3 font-semibold">Waktu</th>
                    <th class="px-4 py-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($notifikasi as $n)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $n->orangTua?->nama ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ $n->no_tujuan }}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $n->siswa?->nama_lengkap ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-800">{{ $n->jenis }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 max-w-xs">
                        <div class="truncate text-xs">{{ $n->isi_pesan }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $color = match($n->status) {
                                'terkirim' => 'emerald',
                                'gagal' => 'red',
                                default => 'amber',
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 rounded text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800">{{ $n->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ $n->created_at?->diffForHumans() }}</td>
                    <td class="px-4 py-3">
                        @if($n->status === 'gagal')
                        <form action="{{ route('whatsapp.resend', $n) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition font-medium">
                                Kirim Ulang
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-slate-500">Belum ada log notifikasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $notifikasi->links() }}
        </div>
    </div>
</div>
@endsection
