@props(['label' => null, 'name', 'value' => '', 'type' => 'text', 'required' => false, 'hint' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm text-sm px-4 py-3 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition placeholder:text-slate-400 ' . ($errors->has($name) ? 'border-red-400 ring-2 ring-red-400/20' : '')]) }}
    >

    @if ($hint && ! $errors->has($name))
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
