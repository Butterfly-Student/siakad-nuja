@extends('layouts.app')

@section('title', 'Tambah Nilai')

@section('content')
<x-page-header title="Tambah Nilai" subtitle="Input nilai baru untuk siswa." />

<x-card>
    <form method="POST" action="{{ route('nilai.store') }}">
        @csrf
        @include('nilai._form')
    </form>
</x-card>
@endsection
