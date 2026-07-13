# 01 — Arsitektur Sistem

> Sumber: BAB II (Landasan Teori) & BAB III (Kerangka Pemikiran, DFD) `KREKSEK_FIXED.docx`.

---

## 1. Gambaran Umum Arsitektur

Sistem terdiri dari **dua subsistem yang saling terintegrasi**:

1. **Aplikasi Web (Laravel 12)** — pengelolaan data akademik dengan 3 tingkat akses
   (Administrator, Guru, Orang Tua/Wali).
2. **Chatbot WhatsApp (rule-based + FSM)** — beroperasi dalam 2 mode:
   - Mode **notifikasi otomatis** (dipicu event: absen, nilai baru, pengumuman, tagihan).
   - Mode **respons interaktif** (orang tua mengecek data via perintah teks).

```
┌──────────────────────────────────────────────────────────────────────┐
│                          PENGGUNA (Browser)                            │
│   Administrator    │    Guru    │    Orang Tua/Wali (Portal Web)        │
└─────────┬──────────────┬──────────────────┬───────────────────────────┘
          │  HTTP/HTTPS (Blade + AJAX)       │
          ▼                                   ▼
┌──────────────────────────────────────────────────────────────────────┐
│                    APLIKASI WEB — LARAVEL 12 (MVC)                      │
│  ┌────────────┐   ┌──────────────┐   ┌───────────────┐                 │
│  │   Routes   │──▶│ Controllers  │──▶│    Services    │                │
│  │ web/api    │   │ (HTTP layer) │   │ (bisnis logic) │                │
│  └────────────┘   └──────┬───────┘   └───────┬────────┘                │
│         Middleware        │ Eloquent ORM      │                        │
│    (auth, role, csrf)     ▼                   ▼                        │
│                    ┌─────────────┐    ┌────────────────┐                │
│                    │   Models    │    │  Notifications │                │
│                    │  (Eloquent) │    │  + Queue/Jobs  │                │
│                    └──────┬──────┘    └───────┬────────┘                │
└───────────────────────────┼───────────────────┼───────────────────────┘
                            │                   │ HTTP POST (kirim pesan)
                            ▼                   ▼
                     ┌────────────┐     ┌──────────────────┐
                     │   MySQL    │     │ WhatsApp Gateway │◀── webhook ──┐
                     │  Database  │     │ (Baileys/Fonnte) │              │
                     └────────────┘     └────────┬─────────┘              │
                                                 │                        │
                                                 ▼                        │
                                        ┌──────────────────┐              │
                                        │ Orang Tua (WA)   │──────────────┘
                                        │  Chatbot 2 arah  │  pesan masuk
                                        └──────────────────┘
```

---

## 2. Pola Arsitektur: MVC (Model-View-Controller)

Sesuai landasan teori (BAB II §2.2.3 & §2.2.7):

| Lapisan | Tanggung jawab | Implementasi Laravel |
|---------|----------------|----------------------|
| **Model** | Data & logika bisnis | Eloquent Models (`app/Models`) |
| **View** | Tampilan antarmuka | Blade templates (`resources/views`) |
| **Controller** | Perantara Model ↔ View | `app/Http/Controllers` |

Ditambah lapisan pendukung khas Laravel: **Service** (logika kompleks), **Request** (validasi),
**Middleware** (auth/role), **Notification + Job/Queue** (WhatsApp), **Repository opsional**.

---

## 3. Komunikasi REST API (BAB II §2.2.15)

REST API berperan sebagai jembatan pada **dua titik**:

1. **Frontend Blade ↔ Backend Laravel** — via AJAX request (GET/POST/PUT/DELETE, format JSON).
   Contoh: filter tabel siswa, submit nilai tanpa reload, verifikasi pembayaran.
2. **Backend Laravel ↔ WhatsApp Gateway** — pengiriman & penerimaan pesan chatbot.
   - **Keluar:** `HTTP POST` dari server Laravel ke *endpoint* gateway.
   - **Masuk:** gateway meneruskan pesan ke *webhook* Laravel (`POST /api/webhook/whatsapp`).

Metode HTTP standar: `GET` (ambil), `POST` (buat), `PUT/PATCH` (perbarui), `DELETE` (hapus).

---

## 4. Alur Integrasi WhatsApp Gateway (BAB II §2.2.5)

```
(1) Event terjadi di Laravel (mis. absensi "Alpha" disimpan)
       │
       ▼
(2) Laravel Notification/Job → HTTP POST ke endpoint gateway
       │        body: { target: "628xxxx", message: "..." }
       ▼
(3) Gateway meneruskan pesan lewat sesi WhatsApp aktif → HP orang tua
       │
       ▼
(4) (interaktif) Orang tua membalas → Gateway kirim webhook POST ke Laravel
       │        body: { sender: "628xxxx", message: "1" }
       ▼
(5) Laravel memproses perintah (FSM) → susun balasan → kembali ke (2)
```

Semua pesan keluar/masuk dicatat ke tabel **`chatbot_logs`** (dan `notifikasi_whatsapp` untuk notifikasi sistem).

---

## 5. Struktur Folder Laravel (usulan)

```
siakad-njk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                      # login, logout, reset password
│   │   │   ├── Admin/                     # DashboardController, SiswaController,
│   │   │   │                              #   GuruController, WaliController,
│   │   │   │                              #   KelasController, MapelController,
│   │   │   │                              #   PengumumanController, SppController,
│   │   │   │                              #   KonfigurasiController, LogWaController
│   │   │   ├── Guru/                      # NilaiController, AbsensiController,
│   │   │   │                              #   RekapController, LaporanController
│   │   │   ├── Wali/                      # PortalController (nilai, absensi, spp, pengumuman)
│   │   │   └── Api/
│   │   │       └── WhatsappWebhookController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php         # cek peran: admin|guru|wali
│   │   └── Requests/                      # Form Request validation
│   ├── Models/                           # Eloquent (lihat 02-skema-database.md)
│   ├── Services/
│   │   ├── Whatsapp/
│   │   │   ├── WhatsappGatewayService.php # kirim pesan ke gateway
│   │   │   └── ChatbotService.php         # FSM + rule-based (lihat 05-chatbot)
│   │   ├── NilaiService.php               # hitung nilai akhir + predikat
│   │   └── LaporanService.php             # generate PDF/Excel
│   ├── Notifications/                     # AbsensiNotification, NilaiNotification, dst.
│   └── Jobs/                              # SendWhatsappMessage (queue)
├── database/
│   ├── migrations/                       # lihat 02-skema-database.md & 06-rencana
│   └── seeders/                          # RoleSeeder, AdminSeeder, KonfigurasiSeeder
├── resources/views/
│   ├── layouts/                          # app, admin, guru, wali
│   ├── auth/
│   ├── admin/                            # dashboard, siswa, guru, wali, kelas, mapel,
│   │                                     #   pengumuman, spp, konfigurasi, log-wa
│   ├── guru/                             # dashboard, nilai, absensi, rekap, laporan
│   └── wali/                             # dashboard, nilai, kehadiran, spp, pengumuman
├── routes/
│   ├── web.php                           # rute web (auth + role)
│   └── api.php                           # webhook whatsapp
└── config/
    └── whatsapp.php                      # konfigurasi gateway (url, token, dsb.)
```

---

## 6. Middleware & Kontrol Akses

- **`auth`** — memastikan pengguna login.
- **`role:admin` / `role:guru` / `role:wali`** — membatasi akses per peran (custom middleware).
- **`csrf`** (bawaan Laravel) — proteksi CSRF token di semua form POST (BAB II §2.2.8).
- Route dikelompokkan per peran:

```php
Route::middleware(['auth','role:admin'])->prefix('admin')->group(...);
Route::middleware(['auth','role:guru'])->prefix('guru')->group(...);
Route::middleware(['auth','role:wali'])->prefix('wali')->group(...);
Route::post('/api/webhook/whatsapp', WhatsappWebhookController::class); // tanpa auth, verifikasi token
```

---

## 7. Antrean (Queue) untuk Notifikasi WhatsApp

Berdasarkan tahap *Revisi Produk Operasional* (BAB III), pengiriman notifikasi WhatsApp
**wajib melalui queue** agar tidak terjadi penumpukan saat banyak pesan dikirim bersamaan
(mis. broadcast pengumuman / tagihan massal).

- Driver queue: `database` (minimal) atau `redis` (disarankan produksi).
- Job: `SendWhatsappMessage` — retry otomatis bila gateway gagal, status dicatat ke log.

---

## 8. Standar Kualitas Acuan

Evaluasi akhir memakai **ISO/IEC 25010** (5 dimensi): Functional Suitability, Usability,
Reliability, Performance Efficiency, User Satisfaction. Lihat `07-requirement-nonfungsional.md`.
