@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<x-page-header title="Tambah Kelas" subtitle="Lengkapi data kelas baru." />

<x-card>
    <form method="POST" action="{{ route('kelas.store') }}" class="space-y-6">
        @csrf
        @include('kelas._form')
    </form>
</x-card>
@endsection
