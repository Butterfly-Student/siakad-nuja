@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Guru</h3>
    <a href="{{ route('guru.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Guru
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>No HP</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guru as $g)
                        <tr>
                            <td>{{ $g->nip }}</td>
                            <td>{{ $g->nama_lengkap }}</td>
                            <td>{{ $g->user->email ?? '-' }}</td>
                            <td>{{ $g->jabatan ?? '-' }}</td>
                            <td>{{ $g->no_hp ?? '-' }}</td>
                            <td>
                                <a href="{{ route('guru.show', $g) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('guru.edit', $g) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('guru.destroy', $g) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $guru->links() }}
    </div>
</div>
@endsection
