@extends('layouts.app')
@section('title', 'Detail Kelas')
@section('content')
<h3 class="mb-3">Detail Kelas</h3>
<div class="card mb-3"><div class="card-body">
    <table class="table">
        <tr><th width="200">Nama Kelas</th><td>{{ $kelas->nama_kelas }}</td></tr>
        <tr><th>Tingkat</th><td>{{ $kelas->tingkat }}</td></tr>
        <tr><th>Jenjang</th><td>{{ $kelas->jenjang }}</td></tr>
        <tr><th>Tahun Ajaran</th><td>{{ $kelas->tahun_ajaran }}</td></tr>
        <tr><th>Wali Kelas</th><td>{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</td></tr>
        <tr><th>Kapasitas</th><td>{{ $kelas->kapasitas ?? '-' }}</td></tr>
        <tr><th>Jumlah Siswa</th><td>{{ $kelas->siswa->count() }}</td></tr>
    </table>
</div></div>

<div class="card"><div class="card-header bg-white"><h6 class="mb-0">Daftar Siswa</h6></div><div class="card-body">
    <table class="table table-sm">
        <thead><tr><th>NIS</th><th>Nama</th></tr></thead>
        <tbody>
            @forelse ($kelas->siswa as $s)
                <tr><td>{{ $s->nis }}</td><td>{{ $s->nama_lengkap }}</td></tr>
            @empty
                <tr><td colspan="2" class="text-center text-muted">Belum ada siswa.</td></tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Kembali</a>
</div></div>
@endsection
