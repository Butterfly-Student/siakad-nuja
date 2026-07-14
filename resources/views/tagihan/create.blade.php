@extends('layouts.app')

@section('title', 'Buat Tagihan')

@section('content')

<x-page-header title="Buat Tagihan" subtitle="Buat tagihan baru untuk siswa atau seluruh kelas.">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('tagihan.index')">
            ← Kembali
        </x-button>
    </x-slot:actions>
</x-page-header>

<div class="mx-auto max-w-3xl">
    <x-card>
        <form method="POST" action="{{ route('tagihan.store') }}">
            @csrf
            @include('tagihan._form')
        </form>
    </x-card>
</div>

@endsection
