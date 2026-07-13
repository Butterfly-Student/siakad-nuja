@extends('layouts.app')
@section('title', 'Absensi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Absensi</h3>
    <a href="{{ route('absensi.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Absensi</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>Tanggal</th><th>Siswa</th><th>Mapel</th><th>Status</th><th>Keterangan</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($absensi as $a)
                @php
                    $badge = ['Hadir'=>'success','Izin'=>'info','Sakit'=>'warning','Alpa'=>'danger'][$a->status ?? ''] ?? 'secondary';
                @endphp
                <tr>
                    <td>{{ optional($a->tanggal)->format('d M Y') }}</td>
                    <td>{{ $a->siswa->nama_lengkap ?? '-' }}</td>
                    <td>{{ $a->jadwal->mapel->nama_mapel ?? '-' }}</td>
                    <td><span class="badge bg-{{ $badge }}">{{ $a->status ?? '-' }}</span></td>
                    <td>{{ $a->keterangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('absensi.show', $a) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('absensi.edit', $a) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('absensi.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
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
    {{ $absensi->links() }}
</div></div>
@endsection
