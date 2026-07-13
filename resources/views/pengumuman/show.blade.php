@extends('layouts.app')
@section('title', 'Detail Pengumuman')
@section('content')
<h3 class="mb-3">{{ $pengumuman->judul }}</h3>
<div class="card"><div class="card-body">
    <div class="mb-2 text-muted small">
        Oleh {{ $pengumuman->pembuat->nama ?? '-' }} —
        {{ optional($pengumuman->created_at)->format('d M Y H:i') }}
        @if ($pengumuman->target_role) — target: {{ $pengumuman->target_role }} @endif
    </div>
    <hr>
    <p style="white-space: pre-wrap;">{{ $pengumuman->konten }}</p>
    <hr>
    <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">Kembali</a>
    <a href="{{ route('pengumuman.edit', $pengumuman) }}" class="btn btn-warning">Edit</a>
</div></div>
@endsection
