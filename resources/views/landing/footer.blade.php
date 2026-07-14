{{-- Footer — Premium dark portfolio footer --}}
<footer class="border-t border-white/5 bg-slate-950">
    <div class="mx-auto max-w-7xl px-5 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-4">

            {{-- Brand --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/30">
                        <x-icon name="mapel" class="h-5 w-5" />
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-black text-white tracking-tight">SIAKAD NUJA</div>
                        <div class="text-[10px] uppercase tracking-[0.25em] text-slate-500">Nurul Jadid · Al-Paiton</div>
                    </div>
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-slate-500">
                    Sistem Informasi Akademik modern untuk pesantren dan sekolah yang ingin bergerak lebih cepat, lebih cerdas, dan lebih transparan.
                </p>
                <div class="mt-6">
                    <div class="text-xs text-slate-600 mb-2">📍 Alamat</div>
                    <p class="text-sm text-slate-400">PP. Nurul Jadid, Karanganyar,<br>Paiton, Probolinggo, Jawa Timur 67291</p>
                </div>
            </div>

            {{-- Navigasi --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Navigasi</h4>
                <ul class="space-y-3 text-sm">
                    @foreach ([
                        '#beranda'   => 'Beranda',
                        '#tentang'   => 'Tentang Sistem',
                        '#fitur'     => 'Fitur Unggulan',
                        '#galeri'    => 'Galeri Tampilan',
                        '#statistik' => 'Pencapaian',
                    ] as $href => $label)
                        <li>
                            <a href="{{ $href }}"
                               class="text-slate-400 transition-colors hover:text-white flex items-center gap-2 group">
                                <span class="h-px w-4 bg-slate-700 transition-all group-hover:w-6 group-hover:bg-brand-500"></span>
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Akses --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Akses Sistem</h4>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-brand-600/20 border border-brand-500/20 px-4 py-2.5 text-sm font-semibold text-brand-300 transition hover:bg-brand-600/30 hover:text-brand-200">
                            <x-icon name="logout" class="h-4 w-4" />
                            Portal Login
                        </a>
                    </li>
                </ul>
                <div class="mt-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Dibangun dengan</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Laravel 11', 'Tailwind CSS', 'Alpine.js', 'MySQL'] as $tech)
                            <span class="rounded-full border border-white/5 bg-white/5 px-3 py-1 text-xs text-slate-400">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="mt-14 flex flex-col items-center justify-between gap-3 border-t border-white/5 pt-8 text-xs text-slate-600 sm:flex-row">
            <p>&copy; {{ date('Y') }} SIAKAD NUJA — PP. Nurul Jadid. Seluruh hak cipta dilindungi.</p>
            <p class="flex items-center gap-1">
                Dibuat dengan <span class="text-rose-500">♥</span> untuk kemajuan pendidikan pesantren.
            </p>
        </div>
    </div>
</footer>
