@extends('layouts.app')

@section('title', 'Buat Pengumuman')

@section('content')
<x-page-header title="Buat Pengumuman" subtitle="Lengkapi informasi pengumuman baru." />

<x-card>
    <form method="POST" action="{{ route('pengumuman.store') }}">
        @csrf
        @include('pengumuman._form')
    </form>
</x-card>
@endsection
