@extends('layouts.app')
@section('title', 'Tambah Mata Pelajaran')
@section('content')
<h3 class="mb-3">Tambah Mata Pelajaran</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('mata-pelajaran.store') }}">
        @include('mata_pelajaran._form')
    </form>
</div></div>
@endsection
