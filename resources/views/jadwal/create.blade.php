@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
<x-page-header title="Tambah Jadwal" subtitle="Lengkapi data jadwal pelajaran baru." />

<x-card>
    <form method="POST" action="{{ route('jadwal.store') }}" class="space-y-6">
        @csrf
        @include('jadwal._form')
    </form>
</x-card>
@endsection
