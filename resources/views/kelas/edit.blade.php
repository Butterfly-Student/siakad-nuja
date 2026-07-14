@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<x-page-header title="Edit Kelas" subtitle="Perbarui data {{ $kelas->nama_kelas }}." />

<x-card>
    <form method="POST" action="{{ route('kelas.update', $kelas) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('kelas._form')
    </form>
</x-card>
@endsection
