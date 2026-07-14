@props(['label' => null, 'value' => 0, 'icon' => 'siswa', 'color' => 'brand', 'href' => null])

@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-900/40 dark:text-sky-300',
        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300',
    ];
    $iconColor = $colors[$color] ?? $colors['brand'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="group flex items-center gap-4 rounded-xl bg-white dark:bg-slate-800 p-5 shadow-sm ring-1 ring-slate-200/70 dark:ring-slate-700/70 {{ $href ? 'transition hover:shadow-md hover:ring-brand-300' : '' }}">
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg {{ $iconColor }}">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <div class="min-w-0">
        <p class="text-sm text-slate-500 dark:text-slate-400 truncate">{{ $label }}</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
    </div>
</{{ $tag }}>
