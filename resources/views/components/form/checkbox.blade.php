@props(['label', 'name', 'checked' => false, 'hint' => null])

<label class="flex items-start gap-3 cursor-pointer select-none">
    <input type="hidden" name="{{ $name }}" value="0">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="1"
        @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-700']) }}
    >
    <span class="text-sm">
        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
        @if ($hint)
            <span class="block text-xs text-slate-400">{{ $hint }}</span>
        @endif
    </span>
</label>
