@extends('layouts.app')

@section('title', 'Rule Chatbot WhatsApp')

@section('content')
<x-page-header title="Rule & Menu Chatbot" subtitle="Kelola menu layanan dan respon otomatis chatbot WhatsApp.">
    <x-slot:actions>
        <x-button :href="route('whatsapp.index')" variant="secondary">
            Kembali ke Gateway
        </x-button>
        <x-button :href="route('whatsapp.chatbot-rules.create')" variant="primary">
            <x-icon name="plus" class="h-4 w-4" /> Tambah Rule / Menu
        </x-button>
    </x-slot:actions>
</x-page-header>

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
    <x-search-bar placeholder="Cari keyword atau nama menu..." />
</div>

<x-card padding="p-0">
    @if ($rules->count())
        {{-- Desktop --}}
        <div class="hidden md:block">
            <x-table :headers="['Urutan', 'Keyword', 'Judul Menu', 'Tipe Aksi', 'Aksi / Respon', 'Status', 'Aksi']">
                @foreach ($rules as $r)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">#{{ $r->urutan }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-xs font-bold px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-brand-600 dark:text-brand-300">
                                {{ $r->keyword }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $r->judul_menu }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($r->tipe_action === 'system_query')
                                <x-badge variant="brand">System Query</x-badge>
                            @else
                                <x-badge variant="info">Static Text</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300 max-w-xs">
                            @if ($r->tipe_action === 'system_query')
                                <code class="text-xs bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-700 dark:text-slate-300">{{ $r->action_key }}</code>
                            @else
                                <div class="truncate text-xs" title="{{ $r->isi_balasan }}">
                                    {{ mb_substr($r->isi_balasan ?? '', 0, 60) }}{{ mb_strlen($r->isi_balasan ?? '') > 60 ? '...' : '' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge :variant="$r->is_active ? 'success' : 'slate'">
                                {{ $r->is_active ? 'Aktif' : 'Nonaktif' }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <x-button :href="route('whatsapp.chatbot-rules.edit', $r)" variant="ghost" size="icon" title="Edit">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </x-button>
                                <x-confirm-delete :action="route('whatsapp.chatbot-rules.destroy', $r)" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        {{-- Mobile --}}
        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 md:hidden">
            @foreach ($rules as $r)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-brand-600">
                                    [{{ $r->keyword }}]
                                </span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $r->judul_menu }}</span>
                            </div>
                            <p class="text-xs text-slate-500">Urutan: #{{ $r->urutan }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <x-button :href="route('whatsapp.chatbot-rules.edit', $r)" variant="ghost" size="icon"><x-icon name="edit" class="h-4 w-4" /></x-button>
                            <x-confirm-delete :action="route('whatsapp.chatbot-rules.destroy', $r)" />
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <x-badge :variant="$r->tipe_action === 'system_query' ? 'brand' : 'info'">
                            {{ $r->tipe_action === 'system_query' ? 'System' : 'Static' }}
                        </x-badge>
                        <x-badge :variant="$r->is_active ? 'success' : 'slate'">
                            {{ $r->is_active ? 'Aktif' : 'Nonaktif' }}
                        </x-badge>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6">
            <x-empty-state icon="whatsapp" title="Belum ada rule chatbot" description="Tambahkan kata kunci dan menu balasan chatbot." />
        </div>
    @endif
</x-card>

@if ($rules->hasPages())
    <div class="mt-4">{{ $rules->links() }}</div>
@endif
@endsection
