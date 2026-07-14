{{-- Marquee — tech stack & fitur chips bergerak --}}
<div class="relative overflow-hidden border-y border-white/5 bg-slate-900/50 py-5 backdrop-blur-sm">
    {{-- Gradient fade kiri & kanan --}}
    <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-24 bg-gradient-to-r from-slate-950 to-transparent"></div>
    <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-24 bg-gradient-to-l from-slate-950 to-transparent"></div>

    <div class="flex animate-[marquee_40s_linear_infinite] whitespace-nowrap">
        @php
            $chips = [
                ['⚡', 'Laravel 11', 'bg-rose-500/10 text-rose-300 border-rose-500/20'],
                ['🎨', 'Tailwind CSS', 'bg-sky-500/10 text-sky-300 border-sky-500/20'],
                ['🏔️', 'Alpine.js', 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20'],
                ['🔔', 'Notifikasi WA', 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'],
                ['📊', 'Dasbor Real-time', 'bg-amber-500/10 text-amber-300 border-amber-500/20'],
                ['💳', 'SPP Digital', 'bg-violet-500/10 text-violet-300 border-violet-500/20'],
                ['📋', 'Absensi Massal', 'bg-brand-500/10 text-brand-300 border-brand-500/20'],
                ['🏆', 'Nilai Otomatis', 'bg-teal-500/10 text-teal-300 border-teal-500/20'],
                ['🗓️', 'Jadwal Koheren', 'bg-orange-500/10 text-orange-300 border-orange-500/20'],
                ['📄', 'Ekspor PDF/Excel', 'bg-pink-500/10 text-pink-300 border-pink-500/20'],
                ['🔐', 'Multi-Peran', 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20'],
                ['🌙', 'Dark Mode', 'bg-slate-500/10 text-slate-300 border-slate-500/20'],
            ];
            $all = array_merge($chips, $chips); // dua putaran
        @endphp
        @foreach ($all as [$em, $label, $cls])
            <span class="mx-3 inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-xs font-semibold {{ $cls }}">
                {{ $em }} {{ $label }}
            </span>
        @endforeach
    </div>
</div>

<style>
@keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
</style>
