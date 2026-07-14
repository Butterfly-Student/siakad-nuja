@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<x-page-header title="Edit Jadwal" subtitle="Perbarui data jadwal pelajaran." />

<x-card>
    <form method="POST" action="{{ route('jadwal.update', $jadwal) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('jadwal._form')
    </form>
</x-card>
@endsection
