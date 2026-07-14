@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<x-page-header title="Edit Siswa" subtitle="Perbarui data {{ $siswa->nama_lengkap }}." />

<x-card>
    <form method="POST" action="{{ route('siswa.update', $siswa) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('siswa._form')
    </form>
</x-card>
@endsection
