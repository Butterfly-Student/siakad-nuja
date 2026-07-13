@extends('layouts.app')
@section('title', 'Edit Guru')
@section('content')
<h3 class="mb-3">Edit Guru</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('guru.update', $guru) }}">
        @method('PUT')
        @include('guru._form')
    </form>
</div></div>
@endsection
