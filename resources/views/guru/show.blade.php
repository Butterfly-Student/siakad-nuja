@extends('layouts.app')
@section('title', 'Detail Guru')
@section('content')
<h3 class="mb-3">Detail Guru</h3>
<div class="card"><div class="card-body">
    <table class="table">
        <tr><th width="200">NIP</th><td>{{ $guru->nip }}</td></tr>
        <tr><th>Nama Lengkap</th><td>{{ $guru->nama_lengkap }}</td></tr>
        <tr><th>Email</th><td>{{ $guru->user->email ?? '-' }}</td></tr>
        <tr><th>Jabatan</th><td>{{ $guru->jabatan ?? '-' }}</td></tr>
        <tr><th>No HP</th><td>{{ $guru->no_hp ?? '-' }}</td></tr>
        <tr><th>Wali Kelas</th><td>{{ $guru->kelasWali->pluck('nama_kelas')->join(', ') ?: '-' }}</td></tr>
    </table>
    <a href="{{ route('guru.index') }}" class="btn btn-secondary">Kembali</a>
    <a href="{{ route('guru.edit', $guru) }}" class="btn btn-warning">Edit</a>
</div></div>
@endsection
