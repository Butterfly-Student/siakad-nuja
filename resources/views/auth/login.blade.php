@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-mortarboard-fill" style="font-size: 3rem; color: #6366f1;"></i>
                    <h3 class="mt-2">SIAKAD NUJA</h3>
                    <p class="text-muted small">Sistem Informasi Akademik</p>
                </div>

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
