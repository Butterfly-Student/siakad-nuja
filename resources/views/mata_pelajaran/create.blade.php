@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')
<x-page-header title="Tambah Mata Pelajaran" subtitle="Lengkapi data mata pelajaran baru." />

<x-card>
    <form method="POST" action="{{ route('mata-pelajaran.store') }}">
        @csrf
        @include('mata_pelajaran._form')
    </form>
</x-card>
@endsection
