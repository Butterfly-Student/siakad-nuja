# 🚂 Panduan Deploy SIAKAD NUJA ke Railway

Panduan lengkap deploy produksi menggunakan **Docker image** khusus yang sudah
menyertakan runtime WhatsApp sidecar (`kstmostofa/laravel-whatsapp` →
whatsapp-web.js + Chromium) di dalam container yang sama dengan aplikasi Laravel.

---

## 1. Arsitektur di Railway

```
                    ┌────────────────────────────────────────────────┐
 Pengguna / WA ────►│  Railway Edge Proxy (HTTPS, domain .up.railway.app)
                    └───────────────┬────────────────────────────────┘
                                    │ HTTP :PORT (dinamis)
                    ┌───────────────▼────────────────────────────────┐
                    │  Service "app"  (image dari Dockerfile ini)     │
                    │                                                 │
                    │  ├─ php artisan serve      → web server :PORT   │
                    │  ├─ queue:work             → worker notifikasi  │
                    │  ├─ whatsapp:sidecar:start → Node :3000 (lokal) │
                    │  │    └─ Chromium headless      ← sesi WA       │
                    │  └─ whatsapp:web:listen    → SSE listener       │
                    │         └─ ChatbotService (FSM) + Observers     │
                    └──────┬──────────────────────────┬───────────────┘
                           │ TCP 3306                 │ Volume (persisten)
                    ┌──────▼──────┐          /var/www/html/storage/app
                    │ MySQL 8     │          ├── public/            (upload bukti)
                    │ (plugin     │          └── whatsapp-sidecar/   (sesi QR!)
                    │  Railway)   │
                    └─────────────┘
```

**Kenapa satu service?** Sesi WhatsApp tersimpan sebagai file LevelDB. Sidecar
dan SSE listener **harus** berbagi folder yang sama, dan Railway Volume hanya
bisa dilekatkan pada satu service. Semua proses dalam satu container membuat
volume tetap sederhana dan sidecar selalu terjangkau lewat `127.0.0.1`.

> Port `3000` milik sidecar tidak pernah dipublikasikan — hanya `$PORT`
> (web server) yang di-expose Railway.

---

## 2. Prasyarat

| Kebutuhan | Keterangan |
|-----------|------------|
| Repo GitHub | Berisi project + file Docker dari panduan ini |
| Akun Railway | Plan Hobby atau lebih (butuh RAM ≥ 2 GB — lihat §8) |
| `APP_KEY` | Generate lokal: `php artisan key:generate --show` |

---

## 3. Langkah Deploy

### Langkah 1 — Push kode ke GitHub

Pastikan file-file berikut sudah ada di repo:

| File | Fungsi |
|------|--------|
| `Dockerfile` | Build image 3 stage: aset frontend → composer → runtime PHP+Node+Chromium |
| `.dockerignore` | Menjaga `.env`, `vendor/`, `node_modules/` keluar dari build context |
| `docker/entrypoint.sh` | Orkestrasi migrasi + web + queue + sidecar + listener |
| `docker/php.ini` | Tuning PHP produksi |
| `railway.toml` | Builder DOCKERFILE + healthcheck `/up` |
| `.env.railway.example` | Template variabel lingkungan Railway |

### Langkah 2 — Buat Project & Database

1. Railway → **New Project** → *Deploy from GitHub repo* → pilih repo `siakad-nuja`.
   Railway otomatis mendeteksi `railway.toml` dan memakai `Dockerfile`.
2. Di project yang sama klik **+ New** → **Database** → **Add MySQL**.
   Pastikan nama service-nya `MySQL` (dipakai oleh referensi variabel).

### Langkah 3 — Set Variabel Lingkungan

Buka service **app** → tab **Variables** → **Raw Editor**, lalu tempel isi
[`.env.railway.example`](../.env.railway.example) dan sesuaikan:

```dotenv
# WAJIB diganti manual:
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx   # php artisan key:generate --show
WHATSAPP_WEB_TOKEN=isi-dengan-openssl-rand-hex-32

# Setelah domain dibuat (Langkah 6), ganti menjadi:
APP_URL=https://siakad-nuja-production.up.railway.app
```

Referensi `${{MySQL.MYSQLHOST}}` dst. otomatis mengambil kredensial database.
Deploy akan ter-trigger ulang setiap kali variabel disimpan.

### Langkah 4 — Lekatkan Volume (WAJIB untuk sesi WhatsApp)

Service **app** → tab **Settings** → **Volumes** → **New Volume**:

| Pengaturan | Nilai |
|------------|-------|
| Mount path | `/var/www/html/storage/app` |
| Size | 1 GB (upload bukti transfer + sesi WA) |

Tanpa volume, setiap redeploy = scan ulang QR WhatsApp.

### Langkah 5 — Ukuran Instance (RAM)

Chromium untuk **satu sesi WhatsApp aktif butuh ±1–2 GB RAM** (dokumentasi
resmi paket). Rekomendasi:

| Komponen | Minimum | Nyaman |
|----------|---------|--------|
| Seluruh container (web+queue+sidecar) | 2 GB | 4 GB |

Settings → **Resources** → naikkan memory limit. Container < 1,5 GB berisiko
di-OOM-kill oleh kernel saat Chromium booting.

### Langkah 6 — Domain Publik

Service **app** → tab **Settings** → **Networking** → **Generate Domain**.
Catat domainnya, lalu update variabel `APP_URL=https://<domain>` agar URL
notifikasi & asset benar.

Healthcheck otomatis menunggu endpoint `/up` sebelum deploy dinyatakan sukses.

### Langkah 7 — Migrasi Data Awal

Migrasi berjalan otomatis tiap boot (`RUN_MIGRATIONS=true`). Untuk data seed
pertama kali, jalankan sekali dari terminal Railway:

```bash
railway run --service app php artisan db:seed --force
# atau via Railway Shell (Dashboard > app > ⋯ > Shell):
php artisan db:seed --force
```

Login admin default: `admin@siakadnuja.sch.id` / `password`
(**segera ganti password!**).

### Langkah 8 — Hubungkan WhatsApp (Scan QR)

1. Login admin → menu **WhatsApp Gateway** (`/whatsapp`) → tombol **Login/Reconnect**.
2. Scan QR yang tampil memakai aplikasi WhatsApp di HP.
3. Status berubah menjadi **connected/ready**. Sesi tersimpan di volume —
   deploy/restart berikutnya **tidak perlu scan lagi**
   (`WHATSAPP_WEB_AUTO_START_SESSIONS=true`).
4. Isi nomor WhatsApp orang tua pada modul **Orang Tua** agar notifikasi terkirim.

Verifikasi kesehatan gateway dari Railway Shell:

```bash
php artisan whatsapp:health        # ok | degraded | down | not_configured
php artisan whatsapp:sidecar:status
```

---

## 4. Proses yang Dikelola Entrypoint

| Proses | Perintah | Watchdog |
|--------|----------|----------|
| Web | `php artisan serve --port=$PORT` | Restart container bila mati (Railway restart policy) |
| Queue | `queue:work --tries=3 --memory=256` | Loop auto-restart 5 detik |
| Sidecar WA | `whatsapp:sidecar:start` (detached) | Cek PID tiap 15 detik, auto start ulang |
| SSE Listener | `whatsapp:web:listen main` | Loop auto-restart 5 detik |

Log tiap komponen: `storage/logs/{wa-listen,queue}.log` dan log deployment
Railway (stdout). Log sidecar mentah: `storage/logs/whatsapp-sidecar*.log`.

Matikan komponen tertentu lewat variabel: `START_QUEUE=false`,
`START_SIDECAR=false`, `RUN_MIGRATIONS=false`.

---

## 5. Keamanan

- ✅ `WHATSAPP_WEB_TOKEN` wajib diganti (min. 32 karakter acak) — auth PHP ↔ Node.
- ✅ Sidecar bind di `127.0.0.1` saja; tidak bisa diakses dari luar container.
- ✅ Panel WhatsApp dilindungi middleware `auth` + role admin (route `routes/web.php:97`).
- ✅ `TRUSTED_PROXIES=*` + `SESSION_SECURE_COOKIE=true` untuk HTTPS di belakang proxy Railway.
- ⚠️ Otomatisasi WhatsApp Web melanggar ToS Meta secara teknis — risiko ban ada
  pada nomor yang dipakai. Gunakan nomor khusus institusi, hindari blast massal.

---

## 6. Backup

Yang hilang = harus di-setup ulang:

| Data | Lokasi | Konsekuensi jika hilang |
|------|--------|------------------------|
| Sesi WhatsApp | Volume `storage/app/whatsapp-sidecar/sessions` | Scan ulang QR |
| Upload bukti bayar | Volume `storage/app/public` | File konfirmasi hilang |
| Database | Plugin MySQL Railway | Gunakan backup bawaan Railway |

Railway menyediakan snapshot volume & backup database — aktifkan keduanya.

---

## 7. Redeploy & Downtime

- Saat redeploy, container baru menggantikan yang lama → WebSocket WhatsApp
  putus sebentar lalu reconnect otomatis; pesan masuk selama gap direplay
  oleh WhatsApp (**tidak hilang**).
- Sesi QR bertahan karena ada di volume.
- Migrasi berjalan otomatis sebelum web server up; healthcheck `/up`
  mencegah trafik masuk ke container yang belum siap.

---

## 8. Troubleshooting

| Gejala | Penyebab & Solusi |
|--------|-------------------|
| Deploy gagal saat healthcheck | Cek log deploy — biasanya DB belum siap atau migrasi error. Naikkan `DB_WAIT_TIMEOUT`. |
| Container restart terus / mati sendiri | OOM karena Chromium. Naikkan RAM ≥ 2 GB (§5). |
| QR muncul terus tiap restart | Volume belum terpasang di `/var/www/html/storage/app`, atau mount path salah. |
| Notifikasi tidak terkirim | `php artisan whatsapp:health`; pastikan queue jalan (`START_QUEUE=true`) dan sesi status ready. |
| Chatbot tidak membalas pesan masuk | SSE listener mati — cek `storage/logs/wa-listen.log`; watchdog akan menghidupkan ulang otomatis. |
| `502` saat pertama buka | Container masih migrasi/scan sesi — tunggu healthcheck hijau. |
| Gambar/upload tidak muncul | Jalankan `php artisan storage:link` dari Railway Shell (dijalankan otomatis oleh entrypoint). |

---

## 9. Uji Image Secara Lokal (opsional)

Simulasi kondisi Railway sebelum push:

```bash
docker compose up --build
# buka http://localhost:8080  (login admin default)
```

Volume `storage_app` di-compose meniru perilaku volume Railway.

---

*Dibuat untuk SIAKAD NUJA — merujuk dokumentasi resmi
[kstmostofa/laravel-whatsapp → Production checklist](https://whatsapp.mostofa.me/production).*
