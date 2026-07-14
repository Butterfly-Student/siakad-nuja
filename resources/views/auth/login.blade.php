@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-slate-100 dark:bg-slate-950 px-4 py-12">
    <div class="w-full max-w-md">
        <div class="mb-8 flex flex-col items-center text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <x-icon name="mapel" class="h-7 w-7" />
            </div>
            <h1 class="mt-4 text-2xl font-bold text-slate-900 dark:text-white">SIAKAD NUJA</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sistem Informasi Akademik Nurul Jadid</p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 p-6 sm:p-8 shadow-sm ring-1 ring-slate-200/70 dark:ring-slate-700/70">
            @if ($errors->any())
                <div class="mb-5">
                    <x-alert type="error">{{ $errors->first() }}</x-alert>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                @csrf
                <x-form.input label="Email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-form.input label="Password" name="password" type="password" required autocomplete="current-password" />

                <div class="flex items-center justify-between">
                    <x-form.checkbox label="Ingat saya" name="remember" />
                </div>

                <x-button type="submit" variant="primary" class="w-full">
                    <x-icon name="logout" class="h-4 w-4" /> Masuk
                </x-button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} SIAKAD NUJA — Akun dibuat oleh administrator.
        </p>
    </div>
</div>
@endsection
