@extends('layouts.app')
@section('title', 'Tambah Kelas')
@section('content')
<h3 class="mb-3">Tambah Kelas</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('kelas.store') }}">
        @include('kelas._form')
    </form>
</div></div>
@endsection
