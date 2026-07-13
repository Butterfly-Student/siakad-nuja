@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('content')
<h3 class="mb-3">Edit Kelas</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('kelas.update', $kelas) }}">
        @method('PUT')
        @include('kelas._form')
    </form>
</div></div>
@endsection
