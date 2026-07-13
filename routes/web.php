<?php

declare(strict_types=1);

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('guru', GuruController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('mata-pelajaran', MataPelajaranController::class)->parameters([
        'mata-pelajaran' => 'mataPelajaran',
    ]);
    Route::resource('siswa', SiswaController::class);
    Route::resource('jadwal', JadwalPelajaranController::class);
    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('orang-tua', OrangTuaController::class)->parameters([
        'orang-tua' => 'orangTua',
    ]);
    Route::resource('nilai', NilaiController::class);
    Route::resource('absensi', AbsensiController::class);
});
