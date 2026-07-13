@extends('layouts.app')
@section('title', 'Orang Tua')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Orang Tua / Wali</h3>
    <a href="{{ route('orang-tua.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>Nama</th><th>Siswa</th><th>Hubungan</th><th>No HP</th><th>Kontak Utama</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($orangTua as $ot)
                <tr>
                    <td>{{ $ot->nama }}</td>
                    <td>{{ $ot->siswa->nama_lengkap ?? '-' }}</td>
                    <td>{{ $ot->hubungan ?? '-' }}</td>
                    <td>{{ $ot->no_hp ?? '-' }}</td>
                    <td>@if ($ot->is_kontak_utama) <span class="badge bg-primary">Ya</span> @else <span class="badge bg-light text-dark">Tidak</span> @endif</td>
                    <td>
                        <a href="{{ route('orang-tua.show', $ot) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('orang-tua.edit', $ot) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('orang-tua.destroy', $ot) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
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
    {{ $orangTua->links() }}
</div></div>
@endsection
