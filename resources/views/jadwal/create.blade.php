@extends('layouts.app')
@section('title', 'Tambah Jadwal')
@section('content')
<h3 class="mb-3">Tambah Jadwal</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('jadwal.store') }}">
        @include('jadwal._form')
    </form>
</div></div>
@endsection
