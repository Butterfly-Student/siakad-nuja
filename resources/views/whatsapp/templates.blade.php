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

<x-card>
    <x-alert />

    <x-table :headers="['Jenis Notifikasi', 'Variabel Dinamis', 'Pratinjau Pesan', 'Aksi']">
        @foreach($templates as $key => $tmpl)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-6 py-4 align-top">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400 font-bold">
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
                            <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $tmpl['label'] }}</div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $key }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 align-top max-w-xs">
                    <div class="text-xs text-brand-600 dark:text-brand-400 font-mono leading-relaxed bg-brand-50/60 dark:bg-brand-950/40 p-2.5 rounded-lg border border-brand-100 dark:border-brand-900/50">
                        {{ $tmpl['hint'] }}
                    </div>
                </td>
                <td class="px-6 py-4 align-top max-w-md">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 p-3 text-xs font-mono text-slate-700 dark:text-slate-300 whitespace-pre-wrap max-h-32 overflow-y-auto">
                        {{ $tmpl['value'] }}
                    </div>
                </td>
                <td class="px-6 py-4 align-top text-right">
                    <x-button :href="route('whatsapp.templates.edit', $key)" variant="secondary" size="sm">
                        <x-icon name="edit" class="h-4 w-4" /> Edit
                    </x-button>
                </td>
            </tr>
        @endforeach
    </x-table>
</x-card>
@endsection
