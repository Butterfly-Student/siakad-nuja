@extends('layouts.app')

@section('title', 'Edit Orang Tua')

@section('content')
<x-page-header title="Edit Orang Tua" subtitle="Perbarui data {{ $orangTua->nama }}." />

<x-card>
    <form method="POST" action="{{ route('orang-tua.update', $orangTua) }}">
        @csrf
        @method('PUT')
        @include('orang_tua._form')
    </form>
</x-card>
@endsection
