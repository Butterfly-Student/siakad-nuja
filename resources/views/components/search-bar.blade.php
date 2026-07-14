@props(['q' => 'q', 'placeholder' => 'Cari...', 'action' => null])

<form method="GET" @if($action) action="{{ $action }}" @endif class="relative w-full sm:max-w-xs">
    @foreach (request()->except([$q, 'page']) as $key => $val)
        @if (! is_array($val))
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endif
    @endforeach
    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
    <input type="search" name="{{ $q }}" value="{{ request($q) }}" placeholder="{{ $placeholder }}"
        class="block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pl-9 pr-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
</form>
