@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('content')
<h3 class="mb-3">Tambah Siswa</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('siswa.store') }}">
        @include('siswa._form')
    </form>
</div></div>
@endsection
