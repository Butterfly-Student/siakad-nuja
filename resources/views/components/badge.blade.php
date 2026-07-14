@props(['variant' => 'slate'])

@php
    $variants = [
        'slate' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        'brand' => 'bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-300',
        'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        'info' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
    ];
    $classes = $variants[$variant] ?? $variants['slate'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
