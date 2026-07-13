@extends('layouts.app')
@section('title', 'Detail Nilai')
@section('content')
<h3 class="mb-3">Detail Nilai</h3>
<div class="card"><div class="card-body">
    <table class="table">
        <tr><th width="200">Siswa</th><td>{{ $nilai->siswa->nama_lengkap ?? '-' }}</td></tr>
        <tr><th>Mapel</th><td>{{ $nilai->mapel->nama_mapel ?? '-' }}</td></tr>
        <tr><th>Kelas</th><td>{{ $nilai->kelas->nama_kelas ?? '-' }}</td></tr>
        <tr><th>Semester</th><td>{{ $nilai->semester }}</td></tr>
        <tr><th>Tahun Ajaran</th><td>{{ $nilai->tahun_ajaran }}</td></tr>
        <tr><th>Nilai Harian</th><td>{{ $nilai->nilai_harian ?? '-' }}</td></tr>
        <tr><th>Nilai UTS</th><td>{{ $nilai->nilai_uts ?? '-' }}</td></tr>
        <tr><th>Nilai UAS</th><td>{{ $nilai->nilai_uas ?? '-' }}</td></tr>
        <tr><th>Nilai Akhir</th><td><strong>{{ $nilai->nilai_akhir ?? '-' }}</strong></td></tr>
        <tr><th>Predikat</th><td><span class="badge bg-primary">{{ $nilai->predikat ?? '-' }}</span></td></tr>
    </table>
    <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Kembali</a>
</div></div>
@endsection
