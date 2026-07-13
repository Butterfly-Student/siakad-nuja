@extends('layouts.app')
@section('title', 'Edit Nilai')
@section('content')
<h3 class="mb-3">Edit Nilai</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('nilai.update', $nilai) }}">
        @method('PUT')
        @include('nilai._form')
    </form>
</div></div>
@endsection
