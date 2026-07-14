@extends('layouts.app')

@section('title', 'Edit Akun')

@section('content')
<x-page-header title="Edit Akun" subtitle="Perbarui akun {{ $user->nama }}." />

<x-card>
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('users._form')
    </form>
</x-card>
@endsection
