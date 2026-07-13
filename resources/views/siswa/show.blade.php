@extends('layouts.app')
@section('title', 'Detail Siswa')
@section('content')
<h3 class="mb-3">Detail Siswa</h3>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0">Data Diri</h6></div><div class="card-body">
            <table class="table">
                <tr><th width="180">NIS</th><td>{{ $siswa->nis }}</td></tr>
                <tr><th>Nama</th><td>{{ $siswa->nama_lengkap }}</td></tr>
                <tr><th>Kelas</th><td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
                <tr><th>JK</th><td>{{ $siswa->jenis_kelamin ?? '-' }}</td></tr>
                <tr><th>Tanggal Lahir</th><td>{{ optional($siswa->tanggal_lahir)->format('d M Y') ?? '-' }}</td></tr>
                <tr><th>Tahun Masuk</th><td>{{ $siswa->tahun_masuk }}</td></tr>
                <tr><th>Status</th><td>{{ $siswa->status ?? '-' }}</td></tr>
                <tr><th>Alamat</th><td>{{ $siswa->alamat ?? '-' }}</td></tr>
            </table>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0">Orang Tua / Wali</h6></div><div class="card-body">
            <ul class="list-group">
                @forelse ($siswa->orangTua as $ot)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $ot->nama }}</strong>
                                <div class="small text-muted">{{ $ot->hubungan ?? '-' }} — {{ $ot->no_hp ?? '-' }}</div>
                            </div>
                            @if ($ot->is_kontak_utama) <span class="badge bg-primary">Utama</span> @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada data.</li>
                @endforelse
            </ul>
        </div></div>

        <div class="card"><div class="card-header bg-white"><h6 class="mb-0">Nilai</h6></div><div class="card-body">
            <table class="table table-sm">
                <thead><tr><th>Mapel</th><th>Semester</th><th>Akhir</th><th>Predikat</th></tr></thead>
                <tbody>
                    @forelse ($siswa->nilai as $n)
                        <tr>
                            <td>{{ $n->mapel->nama_mapel ?? '-' }}</td>
                            <td>{{ $n->semester }}</td>
                            <td>{{ $n->nilai_akhir }}</td>
                            <td>{{ $n->predikat }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada nilai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>
</div>
<a href="{{ route('siswa.index') }}" class="btn btn-secondary">Kembali</a>
@endsection
