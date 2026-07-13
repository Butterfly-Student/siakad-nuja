@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('content')
<h3 class="mb-3">Tambah Guru</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('guru.store') }}">
        @include('guru._form')
    </form>
</div></div>
@endsection
