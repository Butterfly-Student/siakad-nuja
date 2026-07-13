@extends('layouts.app')
@section('title', 'Data Kelas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Kelas</h3>
    <a href="{{ route('kelas.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kelas</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr>
                <th>Nama Kelas</th><th>Tingkat</th><th>Jenjang</th>
                <th>Tahun Ajaran</th><th>Wali Kelas</th><th>Kapasitas</th><th width="180">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse ($kelas as $k)
                    <tr>
                        <td>{{ $k->nama_kelas }}</td>
                        <td>{{ $k->tingkat }}</td>
                        <td>{{ $k->jenjang }}</td>
                        <td>{{ $k->tahun_ajaran }}</td>
                        <td>{{ $k->waliKelas->nama_lengkap ?? '-' }}</td>
                        <td>{{ $k->kapasitas ?? '-' }}</td>
                        <td>
                            <a href="{{ route('kelas.show', $k) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('kelas.edit', $k) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('kelas.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $kelas->links() }}
</div></div>
@endsection
