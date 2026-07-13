@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('content')
<h3 class="mb-3">Edit Siswa</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('siswa.update', $siswa) }}">
        @method('PUT')
        @include('siswa._form')
    </form>
</div></div>
@endsection
