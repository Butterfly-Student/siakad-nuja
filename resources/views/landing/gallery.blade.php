{{-- Galeri — mockup UI showcase dengan auto-scroll carousel --}}
<section id="galeri" class="relative overflow-hidden py-24 sm:py-32">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 -z-10"></div>

    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
        <div class="lp-reveal mx-auto max-w-2xl text-center mb-16">
            <span class="inline-block rounded-full bg-violet-500/15 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-violet-400 mb-5">
                Galeri Sistem
            </span>
            <h2 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl">
                Tampilan yang<br>
                <span class="lp-text-gradient">berbicara sendiri.</span>
            </h2>
            <p class="mt-5 text-slate-400">Antarmuka yang bersih, gelap, dan elegan — bukan desain template yang terasa asing.</p>
        </div>

        {{-- Showcase cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Card: Dashboard Admin --}}
            <div class="lp-reveal group relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-2xl" style="--lp-delay:0ms">
                <div class="relative overflow-hidden" style="padding-top:65%">
                    <div class="absolute inset-0 p-4">
                        <div class="h-full rounded-2xl border border-white/5 bg-slate-800 p-4 text-xs">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-white">Dashboard Admin</span>
                                <div class="flex gap-1"><div class="h-2 w-2 rounded-full bg-rose-500"></div><div class="h-2 w-2 rounded-full bg-amber-500"></div><div class="h-2 w-2 rounded-full bg-emerald-500"></div></div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                @foreach ([['168', 'Siswa', 'brand-500'], ['24', 'Guru', 'emerald-500'], ['8', 'Kelas', 'amber-500'], ['94%', 'Lunas', 'violet-500']] as [$v, $l, $c])
                                    <div class="rounded-xl border border-white/5 bg-white/5 p-2">
                                        <div class="font-black text-white text-sm">{{ $v }}</div>
                                        <div class="text-slate-400 text-[10px]">{{ $l }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="rounded-xl border border-white/5 bg-white/5 p-2">
                                <div class="text-slate-400 mb-1.5">Kehadiran Minggu Ini</div>
                                <div class="flex h-12 items-end gap-0.5">
                                    @foreach ([60,80,45,90,70,100,85] as $h)
                                        <div class="flex-1 rounded-sm bg-brand-500 opacity-80" style="height:{{ $h }}%"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-white/5 p-4">
                    <h3 class="font-bold text-white text-sm">Dashboard Administrator</h3>
                    <p class="text-xs text-slate-400 mt-1">Ringkasan operasional lengkap dengan statistik real-time.</p>
                </div>
            </div>

            {{-- Card: Manajemen Tagihan --}}
            <div class="lp-reveal group relative overflow-hidden rounded-3xl border border-violet-500/20 bg-slate-900 shadow-2xl" style="--lp-delay:120ms">
                <div class="relative overflow-hidden" style="padding-top:65%">
                    <div class="absolute inset-0 p-4">
                        <div class="h-full rounded-2xl border border-white/5 bg-slate-800 p-4 text-xs overflow-hidden">
                            <div class="font-bold text-white mb-3">Tagihan & Pembayaran</div>
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                @foreach ([['Total', '48', 'slate'], ['Lunas', '31', 'emerald'], ['Menunggu', '12', 'amber'], ['Tunggakan', '5', 'rose']] as [$l, $v, $c])
                                    <div class="rounded-xl border border-white/5 bg-white/5 p-2">
                                        <div class="font-black text-{{ $c }}-400 text-sm">{{ $v }}</div>
                                        <div class="text-slate-400 text-[10px]">{{ $l }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="space-y-1.5">
                                @foreach ([['Ahmad R.', 'SPP Juli', 'Menunggu', 'amber'], ['Siti N.', 'SPP Juli', 'Lunas', 'emerald'], ['Budi H.', 'SPP Juni', 'Tunggakan', 'rose']] as [$n, $j, $s, $c])
                                    <div class="flex items-center justify-between rounded-lg bg-white/5 px-2 py-1.5">
                                        <span class="text-white text-[10px]">{{ $n }}</span>
                                        <span class="rounded-full px-2 py-0.5 text-[9px] font-semibold bg-{{ $c }}-500/20 text-{{ $c }}-400">{{ $s }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-white/5 p-4">
                    <h3 class="font-bold text-white text-sm">Manajemen Tagihan</h3>
                    <p class="text-xs text-slate-400 mt-1">Kelola SPP, verifikasi bukti transfer, dan lacak tunggakan.</p>
                </div>
            </div>

            {{-- Card: Input Nilai --}}
            <div class="lp-reveal group relative overflow-hidden rounded-3xl border border-emerald-500/20 bg-slate-900 shadow-2xl" style="--lp-delay:240ms">
                <div class="relative overflow-hidden" style="padding-top:65%">
                    <div class="absolute inset-0 p-4">
                        <div class="h-full rounded-2xl border border-white/5 bg-slate-800 p-4 text-xs overflow-hidden">
                            <div class="font-bold text-white mb-3">Input Nilai · Matematika · 9A</div>
                            <div class="space-y-1.5">
                                @foreach ([
                                    ['Ahmad R.', '88', '80', '92', 'A'],
                                    ['Siti N.', '75', '78', '82', 'B+'],
                                    ['Budi H.', '90', '85', '88', 'A'],
                                    ['Dewi A.', '70', '72', '75', 'B'],
                                ] as [$n, $h, $uts, $uas, $pred])
                                    <div class="flex items-center gap-2 rounded-lg bg-white/5 px-2 py-1.5">
                                        <span class="flex-1 text-white text-[10px] font-medium">{{ $n }}</span>
                                        <span class="text-slate-400 text-[10px]">H:{{ $h }}</span>
                                        <span class="text-slate-400 text-[10px]">UTS:{{ $uts }}</span>
                                        <span class="text-slate-400 text-[10px]">UAS:{{ $uas }}</span>
                                        <span class="rounded-full bg-emerald-500/20 px-1.5 py-0.5 text-[9px] font-bold text-emerald-400">{{ $pred }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2 rounded-lg bg-brand-500/10 border border-brand-500/20 px-2 py-1.5 flex items-center gap-1.5">
                                <div class="h-1.5 w-1.5 rounded-full bg-brand-400 animate-pulse"></div>
                                <span class="text-brand-400 text-[10px]">Notifikasi WA otomatis terkirim</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-white/5 p-4">
                    <h3 class="font-bold text-white text-sm">Input Nilai Akademik</h3>
                    <p class="text-xs text-slate-400 mt-1">Predikat otomatis, WA ke orang tua setelah nilai tersimpan.</p>
                </div>
            </div>

        </div>

    </div>
</section>
