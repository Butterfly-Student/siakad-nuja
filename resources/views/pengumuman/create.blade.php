@extends('layouts.app')
@section('title', 'Buat Pengumuman')
@section('content')
<h3 class="mb-3">Buat Pengumuman</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('pengumuman.store') }}">
        @include('pengumuman._form')
    </form>
</div></div>
@endsection
