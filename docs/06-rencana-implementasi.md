# 06 — Rencana Implementasi Laravel

> Roadmap membangun SIAKAD dengan Laravel 12 + MySQL, mengikuti pendekatan **prototyping**
> (BAB III). Urutan disusun agar dependensi (migration → model → controller → view) terpenuhi.

---

## 1. Persiapan Proyek

```bash
composer create-project laravel/laravel siakad-njk
cd siakad-njk
composer require laravel/breeze --dev   # scaffolding auth (opsional)
php artisan breeze:install blade
composer require barryvdh/laravel-dompdf # export PDF
composer require maatwebsite/excel       # export Excel
```

`.env` (inti):
```
DB_CONNECTION=mysql
DB_DATABASE=siakad_njk
QUEUE_CONNECTION=database
```

`config/whatsapp.php` — url gateway, token, provider (dibaca dari tabel `konfigurasi` saat runtime).

---

## 2. Sprint / Tahapan (selaras 8 tahap R&D Borg & Gall)

| Sprint | Fokus | Deliverable |
|--------|-------|-------------|
| **0. Setup** | Instalasi, auth, role, layout | Login 3 peran, middleware `role`, layout admin/guru/wali |
| **1. Master Data** | Modul 1.1 | CRUD siswa, guru, wali, kelas, mapel, tahun ajaran, jadwal |
| **2. Akademik** | Modul 1.2 | Input nilai (+hitung akhir), absensi, rekap, laporan PDF/Excel |
| **3. Notifikasi WA** | Modul 1.3 (outbound) | Service gateway, Queue Job, notifikasi absensi/nilai |
| **4. Chatbot** | Modul 1.3 (inbound) | Webhook, ChatbotService FSM, sesi & log |
| **5. Keuangan SPP** | tagihan & pembayaran | Tagihan massal, konfirmasi wali, verifikasi admin, kuitansi PDF |
| **6. Pengumuman & Portal Wali** | konten + portal | Pengumuman + target, portal ortu (nilai/kehadiran/spp/pengumuman) |
| **7. Dashboard & Laporan** | Modul 1.4 | Dashboard admin/guru/wali, konfigurasi, log WA |
| **8. Pengujian** | Black Box + UAT | Test cases, kuesioner ISO/IEC 25010, perbaikan |

---

## 3. Migration (lihat `02-skema-database.md` untuk kolom lengkap)

```bash
php artisan make:migration create_users_table            # sesuaikan: +role,+no_hp,+is_active
php artisan make:migration create_tahun_ajaran_table
php artisan make:migration create_mata_pelajaran_table
php artisan make:migration create_guru_table
php artisan make:migration create_kelas_table
php artisan make:migration create_siswa_table
php artisan make:migration create_wali_table
php artisan make:migration create_siswa_wali_table
php artisan make:migration create_guru_mapel_table
php artisan make:migration create_jadwal_table
php artisan make:migration create_komponen_nilai_table
php artisan make:migration create_nilai_table
php artisan make:migration create_absensi_table
php artisan make:migration create_pengumuman_table
php artisan make:migration create_pengumuman_target_table
php artisan make:migration create_tagihan_spp_table
php artisan make:migration create_pembayaran_spp_table
php artisan make:migration create_notifikasi_whatsapp_table
php artisan make:migration create_chatbot_sessions_table
php artisan make:migration create_chatbot_logs_table
php artisan make:migration create_konfigurasi_table
```

## 4. Models & Relasi

```bash
php artisan make:model Siswa
php artisan make:model Guru
php artisan make:model Wali
php artisan make:model Kelas
php artisan make:model MataPelajaran
php artisan make:model TahunAjaran
php artisan make:model Jadwal
php artisan make:model Nilai
php artisan make:model KomponenNilai
php artisan make:model Absensi
php artisan make:model Pengumuman
php artisan make:model PengumumanTarget
php artisan make:model TagihanSpp
php artisan make:model PembayaranSpp
php artisan make:model NotifikasiWhatsapp
php artisan make:model ChatbotSession
php artisan make:model ChatbotLog
php artisan make:model Konfigurasi
```
Definisikan relasi sesuai `02-skema-database.md §6`.

## 5. Controllers

```bash
# Auth
php artisan make:controller Auth/LoginController

# Admin
php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/SiswaController --resource
php artisan make:controller Admin/GuruController --resource
php artisan make:controller Admin/WaliController --resource
php artisan make:controller Admin/KelasController --resource
php artisan make:controller Admin/MapelController --resource
php artisan make:controller Admin/PengumumanController --resource
php artisan make:controller Admin/SppController
php artisan make:controller Admin/KonfigurasiController
php artisan make:controller Admin/LogWaController

# Guru
php artisan make:controller Guru/DashboardController
php artisan make:controller Guru/NilaiController
php artisan make:controller Guru/AbsensiController
php artisan make:controller Guru/RekapController
php artisan make:controller Guru/LaporanController

# Wali
php artisan make:controller Wali/DashboardController
php artisan make:controller Wali/PortalController
php artisan make:controller Wali/PembayaranController

# API / Chatbot
php artisan make:controller Api/WhatsappWebhookController
```

## 6. Services, Jobs, Middleware

```bash
php artisan make:middleware RoleMiddleware
php artisan make:job SendWhatsappMessage
# Services (buat manual di app/Services):
#   Whatsapp/WhatsappGatewayService.php
#   Whatsapp/ChatbotService.php
#   NilaiService.php
#   LaporanService.php
```

## 7. Kerangka Routing (`routes/web.php` & `api.php`)

```php
// web.php
Route::get('/login',[LoginController::class,'show'])->name('login');
Route::post('/login',[LoginController::class,'login']);
Route::post('/logout',[LoginController::class,'logout']);

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class,'index'])->name('dashboard');
    Route::resource('siswa', Admin\SiswaController::class);
    Route::resource('guru', Admin\GuruController::class);
    Route::resource('wali', Admin\WaliController::class);
    Route::resource('kelas', Admin\KelasController::class);
    Route::resource('mapel', Admin\MapelController::class);
    Route::resource('pengumuman', Admin\PengumumanController::class);
    Route::get('spp', [Admin\SppController::class,'index'])->name('spp.index');
    Route::post('spp/bulk', [Admin\SppController::class,'bulk'])->name('spp.bulk');
    Route::post('spp/{pembayaran}/verifikasi', [Admin\SppController::class,'verifikasi']);
    Route::match(['get','post'],'konfigurasi',[Admin\KonfigurasiController::class,'index']);
    Route::get('log-wa',[Admin\LogWaController::class,'index'])->name('logwa');
    Route::post('log-wa/{notif}/resend',[Admin\LogWaController::class,'resend']);
});

Route::middleware(['auth','role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [Guru\DashboardController::class,'index'])->name('dashboard');
    Route::get('nilai',[Guru\NilaiController::class,'index'])->name('nilai');
    Route::post('nilai',[Guru\NilaiController::class,'store']);
    Route::get('absensi',[Guru\AbsensiController::class,'index'])->name('absensi');
    Route::post('absensi',[Guru\AbsensiController::class,'store']);
    Route::get('rekap/nilai',[Guru\RekapController::class,'nilai']);
    Route::get('rekap/kehadiran',[Guru\RekapController::class,'kehadiran']);
    Route::get('laporan',[Guru\LaporanController::class,'index']);
});

Route::middleware(['auth','role:wali'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/', [Wali\DashboardController::class,'index'])->name('dashboard');
    Route::get('nilai',[Wali\PortalController::class,'nilai']);
    Route::get('kehadiran',[Wali\PortalController::class,'kehadiran']);
    Route::get('pengumuman',[Wali\PortalController::class,'pengumuman']);
    Route::get('spp',[Wali\PembayaranController::class,'index']);
    Route::post('spp/konfirmasi',[Wali\PembayaranController::class,'konfirmasi']);
});

// api.php
Route::post('/webhook/whatsapp', [Api\WhatsappWebhookController::class,'handle']);
```

## 8. Seeder Awal

```bash
php artisan make:seeder KonfigurasiSeeder   # info sekolah, template WA, komponen nilai default
php artisan make:seeder AdminSeeder         # akun admin awal
php artisan make:seeder TahunAjaranSeeder   # tahun ajaran aktif
```

## 9. Pengujian (Sprint 8)
- **Black Box Testing:** dokumentasikan input → output → kesesuaian untuk tiap fitur.
- **UAT ISO/IEC 25010:** kuesioner 5 dimensi (lihat `07-requirement-nonfungsional.md`).
- **Automated (opsional):** `php artisan test` untuk `NilaiService` (hitung nilai) & `ChatbotService` (FSM).

---

## 10. Definition of Done per fitur
- [ ] Migration + Model + relasi
- [ ] Validasi (Form Request) di boundary input
- [ ] Controller + View Blade + AJAX bila perlu
- [ ] Middleware role diterapkan
- [ ] Notifikasi WA (bila fitur memicu) via Queue
- [ ] Ekspor PDF/Excel (bila relevan)
- [ ] Uji Black Box lulus
