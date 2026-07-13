@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h3 class="mb-4">Dashboard</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Siswa</div>
                    <div class="h3 mb-0">{{ $stats['total_siswa'] }}</div>
                </div>
                <i class="bi bi-person-badge fs-1 text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3" style="border-left-color: #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Guru</div>
                    <div class="h3 mb-0">{{ $stats['total_guru'] }}</div>
                </div>
                <i class="bi bi-person-workspace fs-1 text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3" style="border-left-color: #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Kelas</div>
                    <div class="h3 mb-0">{{ $stats['total_kelas'] }}</div>
                </div>
                <i class="bi bi-building fs-1 text-warning"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3" style="border-left-color: #ef4444;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Mata Pelajaran</div>
                    <div class="h3 mb-0">{{ $stats['total_mapel'] }}</div>
                </div>
                <i class="bi bi-book fs-1 text-danger"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-megaphone"></i> Pengumuman Terbaru</h5>
    </div>
    <div class="card-body">
        @forelse ($pengumumanTerbaru as $item)
            <div class="border-bottom py-2">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-1">{{ $item->judul }}</h6>
                    <small class="text-muted">{{ $item->created_at?->format('d M Y') }}</small>
                </div>
                <p class="mb-0 small text-muted">{{ \Illuminate\Support\Str::limit($item->konten, 120) }}</p>
                <small class="text-muted">— {{ $item->pembuat->nama ?? 'Anon' }}</small>
            </div>
        @empty
            <p class="text-muted mb-0">Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
@endsection
