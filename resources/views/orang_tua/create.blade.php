@extends('layouts.app')

@section('title', 'Tambah Orang Tua')

@section('content')
<x-page-header title="Tambah Orang Tua" subtitle="Lengkapi data orang tua / wali baru." />

<x-card>
    <form method="POST" action="{{ route('orang-tua.store') }}">
        @csrf
        @include('orang_tua._form')
    </form>
</x-card>
@endsection
