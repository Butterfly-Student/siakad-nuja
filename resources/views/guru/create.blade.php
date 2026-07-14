@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
<x-page-header title="Tambah Guru" subtitle="Lengkapi data guru baru." />

<x-card>
    <form method="POST" action="{{ route('guru.store') }}" class="space-y-6">
        @csrf
        @include('guru._form')
    </form>
</x-card>
@endsection
