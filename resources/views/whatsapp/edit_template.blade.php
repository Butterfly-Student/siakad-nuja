@extends('layouts.app')

@section('title', 'Edit ' . $template['label'])

@section('content')
<x-page-header :title="'Edit ' . $template['label']" subtitle="Kustomisasi isi pesan notifikasi WhatsApp secara presisi. Tersimpan langsung di database.">
    <x-slot:actions>
        <x-button :href="route('whatsapp.templates')" variant="secondary">
            Kembali ke Daftar Template
        </x-button>
    </x-slot:actions>
</x-page-header>

<x-card class="max-w-4xl">
    <form action="{{ route('whatsapp.templates.single-update', $template['key']) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-purple-50/60 dark:bg-purple-950/40 p-4 border border-purple-100 dark:border-purple-900/50 space-y-2">
            <h4 class="text-xs font-bold uppercase tracking-wider text-purple-800 dark:text-purple-300 flex items-center gap-1.5">
                <span>💡</span> Panduan & Variabel Dinamis Tersedia
            </h4>
            <p class="text-xs text-slate-600 dark:text-slate-300">
                Klik variabel di bawah untuk menyisipkannya secara otomatis ke dalam template pesan:
            </p>
            <div class="flex flex-wrap gap-1.5 pt-1">
                @php
                    preg_match_all('/\{([^}]+)\}/', $template['hint'] ?? '', $matches);
                    $vars = !empty($matches[1]) ? array_unique($matches[1]) : [];
                @endphp

                @forelse($vars as $var)
                    <button type="button" onclick="insertVar('{{ '{' . $var . '}' }}')" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-white dark:bg-slate-800 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition cursor-pointer shadow-xs">
                        + {{ '{' . $var . '}' }}
                    </button>
                @empty
                    <span class="text-xs font-mono text-purple-600 dark:text-purple-400">{{ $template['hint'] }}</span>
                @endforelse
            </div>
        </div>

        <div>
            <label for="value" class="block text-sm font-bold text-slate-900 dark:text-white mb-2">
                Isi Lengkap Pesan Template
            </label>
            @if($template['key'] === 'cs_whatsapp')
                <input type="text" id="value" name="value" value="{{ old('value', $template['value']) }}"
                    placeholder="081234567890"
                    class="w-full sm:w-1/2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
            @else
                <textarea id="value" name="value" rows="12"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-mono leading-relaxed">{{ old('value', $template['value']) }}</textarea>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <x-button type="submit" variant="primary">
                <x-icon name="check" class="h-4 w-4" /> Simpan ke Database
            </x-button>
            <x-button variant="secondary" :href="route('whatsapp.templates')">Batal</x-button>
        </div>
    </form>
</x-card>

@push('scripts')
<script>
function insertVar(text) {
    const txtArea = document.getElementById('value');
    if (!txtArea) return;
    const start = txtArea.selectionStart;
    const end = txtArea.selectionEnd;
    const val = txtArea.value;
    txtArea.value = val.substring(0, start) + text + val.substring(end);
    txtArea.selectionStart = txtArea.selectionEnd = start + text.length;
    txtArea.focus();
}
</script>
@endpush
@endsection
