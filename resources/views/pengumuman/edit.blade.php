@extends('layouts.app')
@section('title', 'Edit Pengumuman')
@section('content')
<h3 class="mb-3">Edit Pengumuman</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('pengumuman.update', $pengumuman) }}">
        @method('PUT')
        @include('pengumuman._form')
    </form>
</div></div>
@endsection
