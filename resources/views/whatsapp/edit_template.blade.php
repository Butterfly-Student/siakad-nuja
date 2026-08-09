@extends('layouts.app')

@section('title', 'Edit ' . $template['label'])

@section('content')
<x-page-header :title="'Edit ' . $template['label']" subtitle="Kustomisasi isi pesan notifikasi WhatsApp secara presisi.">
    <x-slot:actions>
        <x-button :href="route('whatsapp.templates')" variant="secondary">
            Kembali ke Daftar Template
        </x-button>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-4xl">
    <x-alert />

    <form action="{{ route('whatsapp.templates.single-update', $template['key']) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-brand-50/60 dark:bg-brand-950/40 p-4 border border-brand-100 dark:border-brand-900/50">
            <h4 class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300 mb-1">
                Panduan Variabel Dinamis
            </h4>
            <p class="text-xs font-mono text-brand-600 dark:text-brand-400">
                {{ $template['hint'] }}
            </p>
        </div>

        <div>
            <label for="value" class="block text-sm font-bold text-slate-900 dark:text-white mb-2">
                Isi Pesan Template
            </label>
            @if($template['key'] === 'cs_whatsapp')
                <input type="text" id="value" name="value" value="{{ old('value', $template['value']) }}"
                    placeholder="081234567890"
                    class="w-full sm:w-1/2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
            @else
                <textarea id="value" name="value" rows="10"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-mono leading-relaxed">{{ old('value', $template['value']) }}</textarea>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <x-button type="submit" variant="primary">
                <x-icon name="check" class="h-4 w-4" /> Simpan Perubahan Template
            </x-button>
            <x-button variant="secondary" :href="route('whatsapp.templates')">Batal</x-button>
        </div>
    </form>
</x-card>
@endsection
