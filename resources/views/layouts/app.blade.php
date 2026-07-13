<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Siakad Nuja') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fb; }
        .sidebar {
            min-height: 100vh;
            background: #1e293b;
            color: #cbd5e1;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: .6rem 1rem;
            border-radius: .5rem;
            margin-bottom: .25rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #334155;
            color: #fff;
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: #fff;
            border-bottom: 1px solid #334155;
        }
        .content { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.06); border-radius: .75rem; }
        .stat-card { border-left: 4px solid #6366f1; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @auth
            <nav class="col-md-2 sidebar p-0 d-flex flex-column">
                <div class="brand">
                    <i class="bi bi-mortarboard-fill"></i> SIAKAD NUJA
                </div>
                <ul class="nav flex-column p-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}" href="{{ route('siswa.index') }}">
                            <i class="bi bi-person-badge me-2"></i> Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guru.*') ? 'active' : '' }}" href="{{ route('guru.index') }}">
                            <i class="bi bi-person-workspace me-2"></i> Guru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}" href="{{ route('kelas.index') }}">
                            <i class="bi bi-building me-2"></i> Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mata-pelajaran.*') ? 'active' : '' }}" href="{{ route('mata-pelajaran.index') }}">
                            <i class="bi bi-book me-2"></i> Mata Pelajaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}" href="{{ route('jadwal.index') }}">
                            <i class="bi bi-calendar-week me-2"></i> Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}" href="{{ route('nilai.index') }}">
                            <i class="bi bi-clipboard-check me-2"></i> Nilai
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}" href="{{ route('absensi.index') }}">
                            <i class="bi bi-check2-square me-2"></i> Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orang-tua.*') ? 'active' : '' }}" href="{{ route('orang-tua.index') }}">
                            <i class="bi bi-people me-2"></i> Orang Tua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}" href="{{ route('pengumuman.index') }}">
                            <i class="bi bi-megaphone me-2"></i> Pengumuman
                        </a>
                    </li>
                </ul>
                <div class="mt-auto p-3 border-top" style="border-color: #334155 !important;">
                    <div class="small mb-2">
                        {{ auth()->user()->nama ?? 'User' }}
                        <div class="text-muted small">{{ ucfirst(auth()->user()->role ?? '') }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-light w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>
            @endauth

            <main class="{{ auth()->check() ? 'col-md-10' : 'col-12' }} content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
