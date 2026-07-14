@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<x-page-header title="Edit Pengumuman" subtitle="Perbarui informasi {{ $pengumuman->judul }}." />

<x-card>
    <form method="POST" action="{{ route('pengumuman.update', $pengumuman) }}">
        @csrf
        @method('PUT')
        @include('pengumuman._form')
    </form>
</x-card>
@endsection
