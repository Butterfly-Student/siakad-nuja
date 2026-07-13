@extends('layouts.app')
@section('title', 'Edit Mata Pelajaran')
@section('content')
<h3 class="mb-3">Edit Mata Pelajaran</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('mata-pelajaran.update', $mapel) }}">
        @method('PUT')
        @include('mata_pelajaran._form')
    </form>
</div></div>
@endsection
