@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Pengumuman</h3>
    <a href="{{ route('pengumuman.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Pengumuman</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>Judul</th><th>Target</th><th>Publish</th><th>Pembuat</th><th>Aktif</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($pengumuman as $p)
                <tr>
                    <td>{{ $p->judul }}</td>
                    <td>{{ $p->target_role ?? 'Semua' }}</td>
                    <td>{{ optional($p->tanggal_publish)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $p->pembuat->nama ?? '-' }}</td>
                    <td>@if ($p->is_active) <span class="badge bg-success">Aktif</span> @else <span class="badge bg-secondary">Nonaktif</span> @endif</td>
                    <td>
                        <a href="{{ route('pengumuman.show', $p) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('pengumuman.edit', $p) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('pengumuman.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $pengumuman->links() }}
</div></div>
@endsection
