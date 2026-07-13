@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Jadwal Pelajaran</h3>
    <a href="{{ route('jadwal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Jadwal</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr>
            <th>Hari</th><th>Jam Ke</th><th>Waktu</th><th>Kelas</th><th>Mapel</th><th>Guru</th><th>Ruangan</th><th width="180">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse ($jadwal as $j)
                <tr>
                    <td>{{ $j->hari }}</td>
                    <td>{{ $j->jam_ke }}</td>
                    <td>{{ \Illuminate\Support\Str::of($j->jam_mulai)->limit(5, '') }} - {{ \Illuminate\Support\Str::of($j->jam_selesai)->limit(5, '') }}</td>
                    <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $j->mapel->nama_mapel ?? '-' }}</td>
                    <td>{{ $j->guru->nama_lengkap ?? '-' }}</td>
                    <td>{{ $j->ruangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('jadwal.show', $j) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('jadwal.edit', $j) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('jadwal.destroy', $j) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    {{ $jadwal->links() }}
</div></div>
@endsection
