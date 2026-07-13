@extends('layouts.app')
@section('title', 'Detail Mata Pelajaran')
@section('content')
<h3 class="mb-3">Detail Mata Pelajaran</h3>
<div class="card"><div class="card-body">
    <table class="table">
        <tr><th width="200">Kode</th><td>{{ $mapel->kode_mapel }}</td></tr>
        <tr><th>Nama</th><td>{{ $mapel->nama_mapel }}</td></tr>
        <tr><th>Jenjang</th><td>{{ $mapel->jenjang }}</td></tr>
        <tr><th>KKM</th><td>{{ $mapel->kkm ?? '-' }}</td></tr>
        <tr><th>Deskripsi</th><td>{{ $mapel->deskripsi ?? '-' }}</td></tr>
    </table>
    <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-secondary">Kembali</a>
</div></div>
@endsection
