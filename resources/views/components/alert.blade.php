@props(['type' => 'success'])

@php
    $config = [
        'success' => ['classes' => 'bg-emerald-50 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200 dark:ring-emerald-800', 'icon' => 'check'],
        'error' => ['classes' => 'bg-red-50 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-200 dark:ring-red-800', 'icon' => 'close'],
        'warning' => ['classes' => 'bg-amber-50 text-amber-800 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-200 dark:ring-amber-800', 'icon' => 'pengumuman'],
        'info' => ['classes' => 'bg-sky-50 text-sky-800 ring-sky-200 dark:bg-sky-900/30 dark:text-sky-200 dark:ring-sky-800', 'icon' => 'pengumuman'],
    ];
    $c = $config[$type] ?? $config['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
     {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg px-4 py-3 text-sm ring-1 $c[classes]"]) }}>
    <x-icon :name="$c['icon']" class="h-5 w-5 shrink-0 mt-0.5" />
    <div class="flex-1">{{ $slot }}</div>
    <button type="button" @click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
        <x-icon name="close" class="h-4 w-4" />
    </button>
</div>
