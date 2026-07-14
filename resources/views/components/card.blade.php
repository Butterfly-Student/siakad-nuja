@props(['padding' => 'p-5 sm:p-6'])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl shadow-sm ring-1 ring-slate-200/70 dark:ring-slate-700/70']) }}>
    @isset($header)
        <div class="border-b border-slate-200 dark:border-slate-700 px-5 sm:px-6 py-4">
            {{ $header }}
        </div>
    @endisset

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-slate-200 dark:border-slate-700 px-5 sm:px-6 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>
