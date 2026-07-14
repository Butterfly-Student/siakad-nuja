@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<x-page-header title="Edit Mata Pelajaran" subtitle="Perbarui data {{ $mapel->nama_mapel }}." />

<x-card>
    <form method="POST" action="{{ route('mata-pelajaran.update', $mapel) }}">
        @csrf
        @method('PUT')
        @include('mata_pelajaran._form')
    </form>
</x-card>
@endsection
