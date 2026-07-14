@extends('layouts.app')
@section('title', 'Template Pesan WhatsApp')
@section('header', 'Template Pesan WhatsApp')

@section('content')
<div class="max-w-3xl">
    <x-alert />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Template Notifikasi</h3>
            <p class="text-sm text-slate-500 mt-0.5">Gunakan placeholder seperti <code class="bg-slate-100 px-1 rounded">{nama_wali}</code>, <code class="bg-slate-100 px-1 rounded">{nama_siswa}</code>, <code class="bg-slate-100 px-1 rounded">{kelas}</code>, <code class="bg-slate-100 px-1 rounded">{status}</code>, <code class="bg-slate-100 px-1 rounded">{tanggal}</code> di dalam template.</p>
        </div>

        <form action="{{ route('whatsapp.templates.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @foreach($templates as $key => $tmpl)
            <div>
                <label for="{{ $key }}" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $tmpl['label'] }}</label>
                @if($key === 'cs_whatsapp')
                    <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $tmpl['value']) }}"
                        placeholder="Contoh: 6281234567890"
                        class="w-full rounded-xl border-slate-200 py-2.5 px-4 focus:ring-2 focus:ring-brand-500 text-sm">
                @else
                    <textarea id="{{ $key }}" name="{{ $key }}" rows="4"
                        class="w-full rounded-xl border-slate-200 py-2.5 px-4 focus:ring-2 focus:ring-brand-500 text-sm font-mono">{{ old($key, $tmpl['value']) }}</textarea>
                @endif
            </div>
            @endforeach

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-brand-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-brand-700 transition shadow-sm">
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
