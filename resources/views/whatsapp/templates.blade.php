@extends('layouts.app')

@section('title', 'Template Notifikasi WhatsApp')

@section('content')
<x-page-header title="Template Notifikasi WhatsApp" subtitle="Kelola isi pesan notifikasi otomatis untuk absensi, nilai, tagihan, pengumuman, dan kuitansi.">
    <x-slot:actions>
        <x-button :href="route('whatsapp.index')" variant="secondary">
            Kembali ke Status Gateway
        </x-button>
    </x-slot:actions>
</x-page-header>

<x-card>
    <x-alert />

    <form action="{{ route('whatsapp.templates.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach($templates as $key => $tmpl)
            <div class="border-b border-slate-100 dark:border-slate-800 pb-6 last:border-0 last:pb-0">
                <label for="{{ $key }}" class="block text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ $tmpl['label'] }}
                </label>
                <p class="text-xs text-brand-600 dark:text-brand-400 mb-2 font-mono">
                    {{ $tmpl['hint'] }}
                </p>

                @if($key === 'cs_whatsapp')
                    <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $tmpl['value']) }}"
                        placeholder="081234567890"
                        class="w-full sm:w-1/2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                @else
                    <textarea id="{{ $key }}" name="{{ $key }}" rows="4"
                        class="w-full rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-mono">{{ old($key, $tmpl['value']) }}</textarea>
                @endif
            </div>
        @endforeach

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <x-button type="submit" variant="primary">
                <x-icon name="check" class="h-4 w-4" /> Simpan Semua Template
            </x-button>
            <x-button variant="secondary" :href="route('whatsapp.index')">Batal</x-button>
        </div>
    </form>
</x-card>
@endsection
