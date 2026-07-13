@extends('layouts.app')
@section('title', 'Data Siswa')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Siswa</h3>
    <a href="{{ route('siswa.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Siswa</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>NIS</th><th>Nama</th><th>Kelas</th><th>JK</th><th>Tahun Masuk</th><th>Status</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($siswa as $s)
                <tr>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->nama_lengkap }}</td>
                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $s->jenis_kelamin ?? '-' }}</td>
                    <td>{{ $s->tahun_masuk }}</td>
                    <td><span class="badge bg-success">{{ $s->status ?? 'Aktif' }}</span></td>
                    <td>
                        <a href="{{ route('siswa.show', $s) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('siswa.edit', $s) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $siswa->links() }}
</div></div>
@endsection
