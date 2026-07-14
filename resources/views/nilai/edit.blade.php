@extends('layouts.app')

@section('title', 'Edit Nilai')

@section('content')
<x-page-header title="Edit Nilai" subtitle="Perbarui nilai {{ $nilai->siswa->nama_lengkap ?? '' }}." />

<x-card>
    <form method="POST" action="{{ route('nilai.update', $nilai) }}">
        @csrf
        @method('PUT')
        @include('nilai._form')
    </form>
</x-card>
@endsection
