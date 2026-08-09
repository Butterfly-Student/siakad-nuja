@extends('layouts.app')

@section('title', 'Tambah Rule Chatbot')

@section('content')
<x-page-header title="Tambah Rule Chatbot" subtitle="Buat menu balasan atau kata kunci baru untuk chatbot WhatsApp.">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('whatsapp.chatbot-rules')">Kembali</x-button>
    </x-slot:actions>
</x-page-header>

<x-card>
    <form action="{{ route('whatsapp.chatbot-rules.store') }}" method="POST">
        @csrf
        @include('whatsapp.rules._form')
    </form>
</x-card>
@endsection
