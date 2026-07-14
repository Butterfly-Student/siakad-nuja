@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')

<x-page-header title="Edit Tagihan" subtitle="Perbarui informasi tagihan.">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('tagihan.show', $tagihan)">
            ← Kembali ke Detail
        </x-button>
    </x-slot:actions>
</x-page-header>

<div class="mx-auto max-w-3xl">
    <x-card>
        <form method="POST" action="{{ route('tagihan.update', $tagihan) }}">
            @csrf
            @method('PUT')
            @include('tagihan._form')
        </form>
    </x-card>
</div>

@endsection
