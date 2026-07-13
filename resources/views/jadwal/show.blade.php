@extends('layouts.app')
@section('title', 'Detail Jadwal')
@section('content')
<h3 class="mb-3">Detail Jadwal</h3>
<div class="card"><div class="card-body">
    <table class="table">
        <tr><th width="200">Hari</th><td>{{ $jadwal->hari }}</td></tr>
        <tr><th>Jam Ke</th><td>{{ $jadwal->jam_ke }}</td></tr>
        <tr><th>Waktu</th><td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td></tr>
        <tr><th>Kelas</th><td>{{ $jadwal->kelas->nama_kelas ?? '-' }}</td></tr>
        <tr><th>Mapel</th><td>{{ $jadwal->mapel->nama_mapel ?? '-' }}</td></tr>
        <tr><th>Guru</th><td>{{ $jadwal->guru->nama_lengkap ?? '-' }}</td></tr>
        <tr><th>Ruangan</th><td>{{ $jadwal->ruangan ?? '-' }}</td></tr>
        <tr><th>Tahun Ajaran</th><td>{{ $jadwal->tahun_ajaran }}</td></tr>
    </table>
    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
</div></div>
@endsection
