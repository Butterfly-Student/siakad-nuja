@extends('layouts.app')

@section('title', 'Edit Guru')

@section('content')
<x-page-header title="Edit Guru" subtitle="Perbarui data {{ $guru->nama_lengkap }}." />

<x-card>
    <form method="POST" action="{{ route('guru.update', $guru) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('guru._form')
    </form>
</x-card>
@endsection
