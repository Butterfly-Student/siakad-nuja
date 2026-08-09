@extends('layouts.app')

@section('title', 'Edit Rule Chatbot')

@section('content')
<x-page-header title="Edit Rule Chatbot" subtitle="Ubah kata kunci, judul menu, atau isi balasan chatbot.">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('whatsapp.chatbot-rules')">Kembali</x-button>
    </x-slot:actions>
</x-page-header>

<x-card>
    <form action="{{ route('whatsapp.chatbot-rules.update', $rule) }}" method="POST">
        @csrf
        @method('PUT')
        @include('whatsapp.rules._form')
    </form>
</x-card>
@endsection
