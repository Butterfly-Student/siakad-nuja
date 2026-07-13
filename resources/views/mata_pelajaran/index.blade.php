@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Mata Pelajaran</h3>
    <a href="{{ route('mata-pelajaran.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>Kode</th><th>Nama Mapel</th><th>Jenjang</th><th>KKM</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($mapel as $m)
                <tr>
                    <td>{{ $m->kode_mapel }}</td>
                    <td>{{ $m->nama_mapel }}</td>
                    <td>{{ $m->jenjang }}</td>
                    <td>{{ $m->kkm ?? '-' }}</td>
                    <td>
                        <a href="{{ route('mata-pelajaran.show', $m) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('mata-pelajaran.edit', $m) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('mata-pelajaran.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $mapel->links() }}
</div></div>
@endsection
