@props(['headers' => []])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        @if (count($headers))
            <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
            {{ $slot }}
        </tbody>
    </table>
</div>
