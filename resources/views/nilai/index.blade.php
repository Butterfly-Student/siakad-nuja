@extends('layouts.app')
@section('title', 'Data Nilai')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Nilai</h3>
    <a href="{{ route('nilai.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Nilai</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>Siswa</th><th>Mapel</th><th>Kelas</th><th>Semester</th>
            <th>Harian</th><th>UTS</th><th>UAS</th><th>Akhir</th><th>Predikat</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($nilai as $n)
                <tr>
                    <td>{{ $n->siswa->nama_lengkap ?? '-' }}</td>
                    <td>{{ $n->mapel->nama_mapel ?? '-' }}</td>
                    <td>{{ $n->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $n->semester }}</td>
                    <td>{{ $n->nilai_harian ?? '-' }}</td>
                    <td>{{ $n->nilai_uts ?? '-' }}</td>
                    <td>{{ $n->nilai_uas ?? '-' }}</td>
                    <td><strong>{{ $n->nilai_akhir ?? '-' }}</strong></td>
                    <td><span class="badge bg-primary">{{ $n->predikat ?? '-' }}</span></td>
                    <td>
                        <a href="{{ route('nilai.show', $n) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('nilai.edit', $n) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('nilai.destroy', $n) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $nilai->links() }}
</div></div>
@endsection
