@extends('layouts.app')

@section('title', 'Tambah Akun')

@section('content')
<x-page-header title="Tambah Akun" subtitle="Buat akun admin atau guru baru." />

<x-card>
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form')
    </form>
</x-card>
@endsection
