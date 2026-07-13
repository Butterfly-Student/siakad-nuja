@extends('layouts.app')
@section('title', 'Edit Jadwal')
@section('content')
<h3 class="mb-3">Edit Jadwal</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('jadwal.update', $jadwal) }}">
        @method('PUT')
        @include('jadwal._form')
    </form>
</div></div>
@endsection
