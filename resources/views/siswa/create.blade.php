@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<x-page-header title="Tambah Siswa" subtitle="Lengkapi data siswa baru." />

<x-card>
    <form method="POST" action="{{ route('siswa.store') }}" enctype="multipart/form-data">
        @csrf
        @include('siswa._form')
    </form>
</x-card>
@endsection
