@extends('layouts.app')

@section('title', 'WhatsApp Gateway')
@section('header', 'WhatsApp Gateway')

@section('content')
<div class="space-y-6 max-w-5xl">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="h-11 w-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800">{{ number_format($totalNotif) }}</div>
                <div class="text-sm text-slate-500">Total Notifikasi</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="h-11 w-11 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800">{{ number_format($totalGagal) }}</div>
                <div class="text-sm text-slate-500">Notifikasi Gagal</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="h-11 w-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800">{{ number_format($totalSesi) }}</div>
                <div class="text-sm text-slate-500">Sesi Chatbot</div>
            </div>
        </div>
    </div>

    {{-- Status Koneksi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800">Status Koneksi Go-WA</h3>
            </div>
            <button id="btn-refresh" class="text-sm text-brand-600 hover:text-brand-700 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>

        <div class="p-6" id="status-container">
            @php
                $statusColor = match($status['status'] ?? 'UNKNOWN') {
                    'CONNECTED' => 'emerald',
                    'DISCONNECTED', 'ERROR' => 'red',
                    'SCAN_QR' => 'amber',
                    default => 'slate',
                };
                $statusLabel = match($status['status'] ?? 'UNKNOWN') {
                    'CONNECTED' => 'Terhubung',
                    'DISCONNECTED' => 'Terputus',
                    'SCAN_QR' => 'Menunggu Login / Scan QR',
                    'ERROR' => 'Error',
                    default => $status['status'] ?? 'Tidak Diketahui',
                };
            @endphp

            <div class="flex items-center gap-4 mb-4">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                    <span class="h-2 w-2 rounded-full bg-{{ $statusColor }}-500 {{ ($status['status'] ?? '') === 'CONNECTED' ? 'animate-pulse' : '' }}"></span>
                    {{ $statusLabel }}
                </span>
                @if(!empty($status['jid']))
                    @php
                        $jidClean = preg_replace('/@.*$/', '', $status['jid']);
                    @endphp
                    <span class="text-sm text-slate-600">Akun: <strong>{{ $jidClean }}</strong></span>
                @endif
                @if(!empty($status['device_id']))
                    <span class="text-xs text-slate-400">Device: {{ $status['device_id'] }}</span>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 mb-4">
                @if(($status['status'] ?? '') !== 'CONNECTED')
                    <form action="{{ route('whatsapp.login') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Login (QR Code)
                        </button>
                    </form>
                @endif

                @if(($status['status'] ?? '') === 'CONNECTED')
                    <form action="{{ route('whatsapp.reconnect') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reconnect
                        </button>
                    </form>

                    <form action="{{ route('whatsapp.logout') }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin logout? Device akan terputus dari WhatsApp.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                @endif
            </div>

            {{-- QR Code Section --}}
            @if($qrUrl)
            <div id="qr-section" class="bg-amber-50 border border-amber-200 rounded-xl p-5 mt-4">
                <p class="text-amber-800 font-semibold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.24M16.12 7.88l1.42-1.42M18.5 12H21m-9 6v-1m0 0h.01m-.01 0h-4m0-6h-.01m.01 4.24V16"/></svg>
                    Scan QR Code ini dengan WhatsApp Anda
                </p>
                <div class="flex gap-6 items-start">
                    <img id="qr-image" src="{{ $qrUrl }}" alt="QR Code" class="w-48 h-48 border border-amber-300 rounded-lg bg-white p-2">
                    <div class="text-sm text-amber-700 space-y-2">
                        <p><strong>Cara menghubungkan:</strong></p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka WhatsApp di HP</li>
                            <li>Tap ikon ⋮ (3 titik) → <em>Linked Devices</em></li>
                            <li>Tap "Link a Device"</li>
                            <li>Scan QR di atas</li>
                        </ol>
                        <p class="mt-3 text-xs">QR auto-refresh setiap 30 detik.</p>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Quick Nav --}}
    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('whatsapp.templates') }}" class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-brand-300 hover:shadow-sm transition flex items-center gap-3 group">
            <div class="h-10 w-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-100 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-slate-800">Template Pesan</div>
                <div class="text-xs text-slate-500">Edit template notifikasi</div>
            </div>
        </a>
        <a href="{{ route('whatsapp.log-notifikasi') }}" class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-brand-300 hover:shadow-sm transition flex items-center gap-3 group">
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-slate-800">Log Notifikasi</div>
                <div class="text-xs text-slate-500">Riwayat & retry pesan gagal</div>
            </div>
        </a>
        <a href="{{ route('whatsapp.log-chatbot') }}" class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-brand-300 hover:shadow-sm transition flex items-center gap-3 group">
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-slate-800">Log Chatbot</div>
                <div class="text-xs text-slate-500">Riwayat percakapan</div>
            </div>
        </a>
    </div>

</div>

@push('scripts')
<script>
// Auto-refresh status setiap 30 detik (untuk QR & connection check)
const statusUrl = "{{ route('whatsapp.status') }}";
let autoRefreshTimer;

async function refreshStatus() {
    try {
        const resp = await fetch(statusUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await resp.json();
        const newStatus = data.status?.status;

        // Update badge (simple reload jika status berubah)
        if (newStatus === 'CONNECTED') {
            // Jika sudah connected, hentikan auto-refresh
            clearInterval(autoRefreshTimer);
            window.location.reload();
        }

        // Update QR jika tersedia (Go-WA mengembalikan URL langsung)
        if (data.qr) {
            const qrImg = document.getElementById('qr-image');
            if (qrImg) qrImg.src = data.qr;
        }
    } catch (e) {
        console.warn('Status refresh error:', e);
    }
}

document.getElementById('btn-refresh')?.addEventListener('click', () => window.location.reload());

// Auto-refresh hanya jika bukan CONNECTED
@if(in_array($status['status'] ?? '', ['SCAN_QR', 'DISCONNECTED']))
autoRefreshTimer = setInterval(refreshStatus, 30000);
@endif
</script>
@endpush
@endsection
