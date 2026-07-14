@props(['action', 'title' => 'Hapus data?', 'message' => 'Tindakan ini tidak dapat dibatalkan.', 'label' => 'Hapus'])

<div x-data="{ open: false }" {{ $attributes }}>
    <button type="button" @click="open = true"
        class="inline-flex items-center justify-center rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:hover:bg-red-900/30"
        title="{{ $label }}">
        <x-icon name="trash" class="h-4 w-4" />
    </button>

    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-slate-900/50"></div>

            <div x-show="open" x-transition
                class="relative w-full max-w-sm rounded-xl bg-white dark:bg-slate-800 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                        <x-icon name="trash" class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $message }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false"
                        class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            {{ $label }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
