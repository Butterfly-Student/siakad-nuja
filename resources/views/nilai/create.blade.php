@extends('layouts.app')
@section('title', 'Tambah Nilai')
@section('content')
<h3 class="mb-3">Tambah Nilai</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('nilai.store') }}">
        @include('nilai._form')
    </form>
</div></div>
@endsection
