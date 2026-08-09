@extends('layouts.app')

@section('title', 'Template Notifikasi WhatsApp')

@section('content')
<x-page-header title="Template Notifikasi WhatsApp" subtitle="Kelola isi pesan notifikasi otomatis untuk absensi, nilai, tagihan, pengumuman, kuitansi, dan teguran wali.">
    <x-slot:actions>
        <x-button :href="route('whatsapp.index')" variant="secondary">
            <x-icon name="whatsapp" class="h-4 w-4" /> Status Gateway
        </x-button>
    </x-slot:actions>
</x-page-header>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 flex items-center justify-center font-bold">
                📝
            </div>
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white">Daftar Template Notifikasi System</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pesan otomatis yang dikirimkan ke wali siswa saat terjadi transaksi/event</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
            {{ count($templates) }} Template Aktif
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-100/70 dark:bg-slate-900/40 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                    <th class="px-6 py-4 w-1/4">Kategori & jenis</th>
                    <th class="px-6 py-4 w-1/4">Variabel Dinamis</th>
                    <th class="px-6 py-4 w-5/12">Pratinjau Format Pesan</th>
                    <th class="px-6 py-4 text-right w-1/12">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-sm">
                @foreach($templates as $key => $tmpl)
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                    {{-- Kategori & Jenis --}}
                    <td class="px-6 py-4 align-top">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400 font-bold text-lg">
                                @if(str_contains($key, 'absensi')) 🔔
                                @elseif(str_contains($key, 'nilai')) 📊
                                @elseif(str_contains($key, 'tagihan')) 💳
                                @elseif(str_contains($key, 'pengumuman')) 📢
                                @elseif(str_contains($key, 'kuitansi')) ✅
                                @elseif(str_contains($key, 'teguran')) ⚠️
                                @else 📞
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white text-sm leading-snug">{{ $tmpl['label'] }}</div>
                                <div class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $key }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Variabel Dinamis --}}
                    <td class="px-6 py-4 align-top">
                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Variabel Tersedia:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    preg_match_all('/\{([^}]+)\}/', $tmpl['hint'] ?? '', $matches);
                                    $vars = !empty($matches[1]) ? array_unique($matches[1]) : [];
                                @endphp

                                @forelse($vars as $var)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-semibold bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300 border border-brand-200/60 dark:border-brand-800/60">
                                        {{ '{' . $var . '}' }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">Tidak ada variabel</span>
                                @endforelse
                            </div>
                        </div>
                    </td>

                    {{-- Pratinjau Format Pesan --}}
                    <td class="px-6 py-4 align-top">
                        <div class="rounded-xl border-l-4 border-l-emerald-500 border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/80 p-3.5 text-xs font-mono text-slate-800 dark:text-slate-200 whitespace-pre-wrap max-h-36 overflow-y-auto leading-relaxed shadow-inner">
                            {{ $tmpl['value'] }}
                        </div>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                        <x-button :href="route('whatsapp.templates.edit', $key)" variant="secondary" size="sm" class="shadow-sm">
                            <x-icon name="edit" class="h-3.5 w-3.5 mr-1" /> Edit
                        </x-button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
