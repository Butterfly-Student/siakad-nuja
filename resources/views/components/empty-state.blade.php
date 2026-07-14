@props(['icon' => 'siswa', 'title' => 'Belum ada data', 'description' => null])

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
        <x-icon :name="$icon" class="h-7 w-7 text-slate-400 dark:text-slate-500" />
    </div>
    <h3 class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
