@extends('layouts.app')
@section('title', 'Tambah Orang Tua')
@section('content')
<h3 class="mb-3">Tambah Orang Tua</h3>
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('orang-tua.store') }}">
        @include('orang_tua._form')
    </form>
</div></div>
@endsection
