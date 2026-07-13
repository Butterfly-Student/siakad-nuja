@extends('layouts.app')
@section('title', 'Detail Orang Tua')
@section('content')
<h3 class="mb-3">Detail Orang Tua</h3>
<div class="card"><div class="card-body">
    <table class="table">
        <tr><th width="200">Siswa</th><td>{{ $orangTua->siswa->nama_lengkap ?? '-' }}</td></tr>
        <tr><th>Nama</th><td>{{ $orangTua->nama }}</td></tr>
        <tr><th>Hubungan</th><td>{{ $orangTua->hubungan ?? '-' }}</td></tr>
        <tr><th>No HP</th><td>{{ $orangTua->no_hp ?? '-' }}</td></tr>
        <tr><th>Pekerjaan</th><td>{{ $orangTua->pekerjaan ?? '-' }}</td></tr>
        <tr><th>Alamat</th><td>{{ $orangTua->alamat ?? '-' }}</td></tr>
        <tr><th>Kontak Utama</th><td>{{ $orangTua->is_kontak_utama ? 'Ya' : 'Tidak' }}</td></tr>
    </table>
    <a href="{{ route('orang-tua.index') }}" class="btn btn-secondary">Kembali</a>
</div></div>
@endsection
