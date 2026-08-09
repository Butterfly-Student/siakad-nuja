@extends('layouts.app')

@section('title', 'Template Notifikasi WhatsApp')

@section('content')
<x-page-header title="Template Notifikasi WhatsApp" subtitle="Kelola isi pesan notifikasi otomatis untuk absensi, nilai, tagihan, pengumuman, kuitansi, dan teguran wali." />

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-3.5 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 flex items-center justify-center font-bold text-sm">
                📝
            </div>
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white text-sm">Daftar Template Notifikasi System</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Tersimpan di Database (Tabel Konfigurasi)</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
            {{ count($templates) }} Template Aktif
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-100/70 dark:bg-slate-900/40 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">
                    <th class="px-5 py-3 w-1/4">Kategori & Jenis</th>
                    <th class="px-5 py-3 w-1/4">Variabel Dinamis</th>
                    <th class="px-5 py-3 w-5/12">Ringkasan Format Pesan</th>
                    <th class="px-5 py-3 text-right w-1/12">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs">
                @foreach($templates as $key => $tmpl)
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                    {{-- Kategori & Jenis --}}
                    <td class="px-5 py-3 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400 font-bold text-sm">
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
                                <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">{{ $tmpl['label'] }}</div>
                                <div class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-mono font-medium bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400">
                                    {{ $key }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Variabel Dinamis --}}
                    <td class="px-5 py-3 align-middle">
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @php
                                preg_match_all('/\{([^}]+)\}/', $tmpl['hint'] ?? '', $matches);
                                $vars = !empty($matches[1]) ? array_unique($matches[1]) : [];
                            @endphp

                            @forelse(array_slice($vars, 0, 4) as $var)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300 border border-brand-200/50 dark:border-brand-800/50">
                                    {{ '{' . $var . '}' }}
                                </span>
                            @empty
                                <span class="text-[11px] text-slate-400 italic">Tidak ada variabel</span>
                            @endforelse

                            @if(count($vars) > 4)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    +{{ count($vars) - 4 }} lagi
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Ringkasan Format Pesan (Compact Excerpt) --}}
                    <td class="px-5 py-3 align-middle">
                        <div class="rounded-lg border-l-2 border-l-emerald-500 border border-slate-200 dark:border-slate-700 bg-slate-50/90 dark:bg-slate-900/90 px-3 py-2 text-[11px] font-mono text-slate-700 dark:text-slate-300 line-clamp-2 max-h-12 overflow-hidden leading-relaxed">
                            {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $tmpl['value']), 90) }}
                        </div>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-3 align-middle text-right whitespace-nowrap">
                        <x-button :href="route('whatsapp.templates.edit', $key)" variant="secondary" size="xs" class="shadow-xs font-semibold px-2.5 py-1 text-xs">
                            <x-icon name="edit" class="h-3 w-3 mr-1" /> Edit
                        </x-button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
