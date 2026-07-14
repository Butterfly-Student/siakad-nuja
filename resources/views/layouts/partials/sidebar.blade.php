@php
    $user = auth()->user();
    $isAdmin = $user?->isAdmin() ?? false;

    // Definisi menu navigasi berdasarkan peran
    $navSections = [
        [
            'label' => null,
            'items' => [
                ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'show' => true],
            ],
        ],
        [
            'label' => 'Akademik',
            'items' => [
                ['route' => 'nilai.index', 'match' => 'nilai.*', 'icon' => 'nilai', 'label' => 'Nilai', 'show' => true],
                ['route' => 'absensi.index', 'match' => 'absensi.*', 'icon' => 'absensi', 'label' => 'Absensi', 'show' => true],
                ['route' => 'jadwal.index', 'match' => 'jadwal.*', 'icon' => 'jadwal', 'label' => 'Jadwal', 'show' => true],
                ['route' => 'laporan.index', 'match' => 'laporan.*', 'icon' => 'download', 'label' => 'Laporan Akademik', 'show' => true],
            ],
        ],
        [
            'label' => 'Data Master',
            'items' => [
                ['route' => 'siswa.index', 'match' => 'siswa.*', 'icon' => 'siswa', 'label' => 'Siswa', 'show' => true],
                ['route' => 'guru.index', 'match' => 'guru.*', 'icon' => 'guru', 'label' => 'Guru', 'show' => $isAdmin],
                ['route' => 'kelas.index', 'match' => 'kelas.*', 'icon' => 'kelas', 'label' => 'Kelas', 'show' => true],
                ['route' => 'mata-pelajaran.index', 'match' => 'mata-pelajaran.*', 'icon' => 'mapel', 'label' => 'Mata Pelajaran', 'show' => true],
                ['route' => 'orang-tua.index', 'match' => 'orang-tua.*', 'icon' => 'orangtua', 'label' => 'Orang Tua', 'show' => $isAdmin],
            ],
        ],
        [
            'label' => 'Keuangan',
            'items' => [
                ['route' => 'tagihan.index', 'match' => 'tagihan.*', 'icon' => 'tagihan', 'label' => 'Tagihan & Pembayaran', 'show' => $isAdmin],
            ],
        ],
        [
            'label' => 'Lainnya',
            'items' => [
                ['route' => 'pengumuman.index', 'match' => 'pengumuman.*', 'icon' => 'pengumuman', 'label' => 'Pengumuman', 'show' => true],
                ['route' => 'users.index', 'match' => 'users.*', 'icon' => 'users', 'label' => 'Manajemen Akun', 'show' => $isAdmin],
            ],
        ],
    ];
@endphp

<nav x-data class="flex h-full flex-col bg-slate-900 text-slate-300">
    <div class="flex items-center gap-2.5 px-5 h-16 border-b border-slate-800 shrink-0">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white">
            <x-icon name="mapel" class="h-5 w-5" />
        </div>
        <div class="leading-tight">
            <div class="font-bold text-white">SIAKAD NUJA</div>
            <div class="text-[10px] uppercase tracking-wider text-slate-500">Nurul Jadid</div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        @foreach ($navSections as $section)
            @php $visible = collect($section['items'])->where('show', true); @endphp
            @if ($visible->isNotEmpty())
                <div>
                    @if ($section['label'])
                        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $section['label'] }}</p>
                    @endif
                    <ul class="space-y-1">
                        @foreach ($visible as $item)
                            @php $active = request()->routeIs($item['match']); @endphp
                            <li>
                                <a href="{{ route($item['route']) }}"
                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ $active ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>

    <div class="border-t border-slate-800 p-3 shrink-0">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-800">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-700 text-sm font-semibold text-white">
                {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-medium text-white">{{ $user->nama ?? 'User' }}</div>
                <div class="text-xs capitalize text-slate-400">{{ $user->role ?? '' }}</div>
            </div>
        </a>
    </div>
</nav>
