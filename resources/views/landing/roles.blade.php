{{-- Peran pengguna — Split screen immersive panels + Orang Tua --}}
<section id="peran" class="relative py-24 sm:py-32 overflow-hidden">
    <div class="absolute inset-0 bg-slate-950 -z-10"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-full bg-gradient-to-b from-transparent via-white/5 to-transparent"></div>

    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="lp-reveal mx-auto max-w-2xl text-center mb-16">
            <span class="inline-block rounded-full bg-white/5 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-slate-400 mb-5 border border-white/10">
                Pengguna Sistem
            </span>
            <h2 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl">
                Tiga peran,<br>
                <span class="lp-text-gradient">satu tujuan.</span>
            </h2>
            <p class="mt-5 text-slate-400">Setiap pengguna hanya melihat yang relevan dengan perannya.</p>
        </div>

        {{-- Tiga panel --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Administrator --}}
            <div class="lp-reveal group relative overflow-hidden rounded-3xl border border-brand-500/20 bg-gradient-to-b from-brand-950/70 to-slate-900 p-8">
                <div class="absolute -right-12 -top-12 h-52 w-52 rounded-full bg-brand-500/10 blur-3xl pointer-events-none group-hover:bg-brand-500/20 transition duration-700"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex h-13 w-13 items-center justify-center rounded-2xl bg-brand-600 shadow-lg shadow-brand-600/30 text-white" style="width:52px;height:52px">
                            <x-icon name="users" class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white">Administrator</h3>
                            <p class="text-sm text-brand-400">Kendali penuh sistem</p>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        @foreach ([
                            'Kelola data siswa, guru, kelas & mapel',
                            'Manajemen tagihan & verifikasi SPP',
                            'Buat pengumuman & broadcast WA',
                            'Pantau statistik akademik global',
                            'Manajemen akun & hak akses',
                            'Konfigurasi sistem & template WA',
                        ] as $item)
                            <li class="flex items-start gap-2.5 text-sm">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-500/20 text-brand-400">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                                <span class="text-slate-300">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Guru --}}
            <div class="lp-reveal group relative overflow-hidden rounded-3xl border border-emerald-500/20 bg-gradient-to-b from-emerald-950/60 to-slate-900 p-8" style="--lp-delay:100ms">
                <div class="absolute -right-12 -top-12 h-52 w-52 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none group-hover:bg-emerald-500/20 transition duration-700"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex items-center justify-center rounded-2xl bg-emerald-600 shadow-lg shadow-emerald-600/30 text-white" style="width:52px;height:52px">
                            <x-icon name="guru" class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white">Guru</h3>
                            <p class="text-sm text-emerald-400">Fokus pada kelas diampu</p>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        @foreach ([
                            'Input & edit nilai kelas yang diampu',
                            'Absensi massal per jadwal & tanggal',
                            'Rekap kehadiran & rekap nilai kelas',
                            'Laporan akademik PDF/Excel',
                            'Lihat data siswa, kelas & jadwal',
                            'Dasbor jadwal mengajar hari ini',
                        ] as $item)
                            <li class="flex items-start gap-2.5 text-sm">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                                <span class="text-slate-300">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Orang Tua/Wali --}}
            <div class="lp-reveal group relative overflow-hidden rounded-3xl border border-amber-500/20 bg-gradient-to-b from-amber-950/50 to-slate-900 p-8" style="--lp-delay:200ms">
                <div class="absolute -right-12 -top-12 h-52 w-52 rounded-full bg-amber-500/10 blur-3xl pointer-events-none group-hover:bg-amber-500/20 transition duration-700"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex items-center justify-center rounded-2xl bg-amber-600 shadow-lg shadow-amber-600/30 text-white" style="width:52px;height:52px">
                            <x-icon name="orangtua" class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white">Orang Tua / Wali</h3>
                            <p class="text-sm text-amber-400">Pantau perkembangan anak</p>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        @foreach ([
                            'Lihat detail nilai akademik anak',
                            'Pantau kehadiran per bulan',
                            'Terima notifikasi absen via WA',
                            'Cek status tagihan & riwayat SPP',
                            'Konfirmasi pembayaran online',
                            'Baca pengumuman sekolah',
                        ] as $item)
                            <li class="flex items-start gap-2.5 text-sm">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-400">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                                <span class="text-slate-300">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>
