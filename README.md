<div align="center">
  <h1>🏫 SIAKAD NUJA</h1>
  <p><strong>Sistem Informasi Akademik Siswa</strong><br>
  Yayasan Nurul Jadid Karduluk — Terintegrasi Chatbot WhatsApp</p>

  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=flat-square&logo=tailwindcss" alt="Tailwind v4">
  <img src="https://img.shields.io/badge/WhatsApp-WAHA-25D366?style=flat-square&logo=whatsapp" alt="WAHA">
</div>

---

## 📖 Tentang Proyek

SIAKAD NUJA adalah aplikasi web terpusat untuk mengelola seluruh data akademik sekolah, mulai dari data siswa, guru, kelas, mata pelajaran, jadwal, nilai, kehadiran, tagihan SPP, hingga pengumuman. Sistem ini terintegrasi dengan **chatbot WhatsApp** untuk notifikasi otomatis dan layanan informasi interaktif bagi orang tua/wali.

**Tiga Peran Pengguna:**
| Peran | Akses |
|-------|-------|
| **Admin** | Kelola seluruh data master, keuangan/SPP, pengumuman, konfigurasi WhatsApp |
| **Guru** | Input nilai & absensi kelas yang diampu, laporan akademik |
| **Orang Tua** | Portal pemantauan anak + chatbot WhatsApp interaktif |

---

## ✨ Fitur Unggulan

- 🎨 **Landing page modern** — animasi, glassmorphism, dark mode
- 🔐 **Autentikasi role-based** — Admin / Guru (tanpa paket pihak ketiga)
- 📊 **Nilai otomatis** — NH 30% · UTS 30% · UAS 40%, predikat & status tuntas/remedial
- 📋 **Absensi massal** — satu kelas satu kali submit
- 💳 **Tagihan & Pembayaran** — konfirmasi transfer dengan upload bukti
- 📄 **Laporan PDF** — rekap nilai & kehadiran kelas (dompdf)
- 💬 **Chatbot WhatsApp (FSM)** — multi-anak, notifikasi otomatis (absensi alpa, nilai, tagihan, pengumuman)
- 📡 **Admin Panel WhatsApp** — status koneksi WAHA, scan QR, template pesan, log notifikasi

---

## 🧩 Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL 8.x |
| Frontend | Blade + Tailwind CSS v4 + Alpine.js + Vite |
| WhatsApp | WAHA (self-hosted Docker) |
| PDF | barryvdh/laravel-dompdf |
| Queue | Laravel Queue (driver: `database`) |

---

## ⚡ Prasyarat

Pilih salah satu metode instalasi di bawah sesuai kebutuhan Anda.

| Software | Versi | Keterangan |
|----------|-------|------------|
| PHP | ≥ 8.2 | Ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip` |
| Composer | 2.x | Manajer paket PHP |
| Node.js | ≥ 20 | Runtime JavaScript untuk Vite/Tailwind |
| MySQL | ≥ 8.0 | Atau MariaDB 10.4+ |
| Git | terbaru | Untuk clone repository |

---

## 🚀 Instalasi

### Opsi 1 — Laragon (Windows, Direkomendasikan untuk Development)

Laragon adalah environment development all-in-one untuk Windows yang sudah menyertakan PHP, MySQL, Composer, dan server Nginx/Apache.

**1. Unduh & Install Laragon**

Unduh dari: https://laragon.org/download/  
Pilih **Laragon Full** untuk mendapatkan PHP, MySQL, Node.js, dan Composer sekaligus.

**2. Clone Repositori**

Tempatkan proyek di folder `www` Laragon (default: `C:\laragon\www`):

```bash
# Buka terminal di dalam Laragon atau Git Bash
cd C:\laragon\www
git clone https://github.com/your-repo/siakad-nuja.git
cd siakad-nuja
```

Laragon otomatis membuat virtual host. Akses via: `http://siakad-nuja.test`

**3. Install Dependency**

```bash
composer install
npm install
```

**4. Konfigurasi Environment**

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env` — ubah bagian database:

```dotenv
APP_NAME=SiakadNuja
APP_URL=http://siakad-nuja.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad_nuja
DB_USERNAME=root
DB_PASSWORD=            # Laragon default: kosong

QUEUE_CONNECTION=database
```

**5. Buat Database**

Buka Laragon → klik kanan tray icon → **Database** (HeidiSQL/TablePlus akan terbuka).  
Buat database baru bernama `siakad_nuja`.

**6. Migrasi & Seed**

```bash
php artisan migrate --seed
php artisan storage:link
```

**7. Build Aset**

```bash
# Development (hot reload)
npm run dev

# atau Production build
npm run build
```

**8. Jalankan Aplikasi**

Laragon sudah menjalankan Apache/Nginx otomatis.  
Buka browser: **http://siakad-nuja.test**

Atau gunakan built-in server PHP:
```bash
php artisan serve
```
Akses: **http://127.0.0.1:8000**

---

### Opsi 2 — Docker (Cross-Platform)

> Pastikan **Docker Desktop** sudah terinstal dan berjalan.

**1. Clone Repositori**

```bash
git clone https://github.com/your-repo/siakad-nuja.git
cd siakad-nuja
```

**2. Konfigurasi Environment**

```bash
cp .env.example .env
```

Edit `.env` untuk konfigurasi Docker:

```dotenv
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql          # nama service Docker, bukan 127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad_nuja
DB_USERNAME=siakad
DB_PASSWORD=secret

QUEUE_CONNECTION=database
```

**3. Gunakan Laravel Sail (Opsional)**

```bash
composer install --ignore-platform-reqs
php artisan sail:install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Akses: **http://localhost**

**4. Tanpa Sail (Manual Docker Compose)**

Buat file `docker-compose.yml` di root proyek:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
    environment:
      - DB_HOST=mysql

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: siakad_nuja
      MYSQL_USER: siakad
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose exec app npm install
docker compose exec app npm run build
```

---

### Opsi 3 — Manual (XAMPP / php artisan serve)

```bash
# 1. Clone
git clone https://github.com/your-repo/siakad-nuja.git
cd siakad-nuja

# 2. Install dependencies
composer install
npm install

# 3. Environment
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate

# 4. Atur .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
# 5. Buat database 'siakad_nuja' di phpMyAdmin/MySQL

# 6. Migrasi + seed
php artisan migrate --seed
php artisan storage:link

# 7. Build aset
npm run build

# 8. Jalankan
php artisan serve
```

---

## 🔑 Akun Default (Setelah Seed)

| Peran | Email / Username | Password |
|-------|-----------------|----------|
| **Admin** | `admin@siakadnuja.sch.id` | `password` |
| **Guru 1–10** | `guru1@siakadnuja.sch.id` … `guru10@siakadnuja.sch.id` | `password` |

> Data seed mencakup ±168 siswa, 10 guru, 6 kelas, 10 mata pelajaran, 180 jadwal, serta nilai & absensi acak.

---

## 💬 Integrasi WhatsApp (WAHA)

Fitur chatbot & notifikasi WhatsApp menggunakan **WAHA** (WhatsApp HTTP API) sebagai gateway self-hosted.

### 1. Jalankan Container WAHA

```bash
docker run -d \
  --name waha \
  --restart unless-stopped \
  -p 3000:3000 \
  devlikeapro/waha
```

Windows (PowerShell):
```powershell
docker run -d --name waha --restart unless-stopped -p 3000:3000 devlikeapro/waha
```

### 2. Konfigurasi `.env`

```dotenv
WAHA_URL=http://localhost:3000
WAHA_SESSION=default
WAHA_API_KEY=            # kosong untuk versi Core (gratis)
```

### 3. Hubungkan WhatsApp

1. Buka **http://localhost:3000/dashboard** — dashboard WAHA
2. Atau buka admin panel SIAKAD → menu **WhatsApp Gateway** → scan QR yang tampil

### 4. Daftarkan Webhook ke WAHA

```bash
# Agar WAHA mengirim pesan masuk ke Laravel
curl -X PUT http://localhost:3000/api/sessions/default/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://host.docker.internal:8000/api/webhook/whatsapp",
    "events": ["message"]
  }'
```

> Di produksi, ganti `host.docker.internal:8000` dengan URL server Anda.

### 5. Isi Nomor WA Orang Tua

Di admin panel → **Orang Tua** → edit data orang tua → isi kolom **Nomor WhatsApp**.

### Queue Worker (untuk pengiriman async)

```bash
php artisan queue:work --tries=3
```

> Untuk produksi, gunakan Supervisor atau Laravel Horizon.

---

## ▶️ Menjalankan Mode Development

Jalankan **dua terminal** secara bersamaan:

```bash
# Terminal 1 — Laravel backend
php artisan serve

# Terminal 2 — Vite (hot reload CSS & JS)
npm run dev
```

Buka **http://127.0.0.1:8000**

---

## 🗂️ Struktur Folder Penting

```
siakad-nuja/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/WhatsappWebhookController.php   # Webhook inbound WAHA
│   │   └── WhatsappController.php              # Admin panel WhatsApp
│   ├── Jobs/SendWhatsappMessage.php            # Queue job kirim WA
│   ├── Models/                                 # Eloquent models
│   ├── Observers/                              # Notifikasi otomatis (Absensi, Nilai, dll)
│   ├── Providers/AppServiceProvider.php        # Registrasi observer
│   └── Services/
│       ├── ChatbotService.php                  # FSM chatbot (rule-based)
│       └── WhatsappGatewayService.php          # Integrasi WAHA API
├── config/whatsapp.php                         # Konfigurasi gateway
├── database/
│   ├── migrations/                             # Skema tabel lengkap
│   └── seeders/                                # Data awal & template WA
├── docs/                                       # Dokumentasi teknis lengkap
│   ├── 00-ringkasan-proyek.md
│   ├── 02-skema-database.md
│   ├── 05-chatbot-whatsapp.md
│   └── panduan-waha-docker.md                  # Panduan Docker WAHA
├── resources/views/
│   ├── landing/                                # Halaman publik
│   ├── layouts/                                # Layout & sidebar
│   ├── whatsapp/                               # Admin panel WA (index, templates, log)
│   └── <modul>/                                # View tiap modul
└── routes/
    ├── web.php                                 # Rute web (auth + role)
    └── api.php                                 # Webhook WhatsApp
```

---

## 👥 Hak Akses Per Modul

| Modul | Admin | Guru |
|-------|:-----:|:----:|
| Dashboard | ✅ | ✅ |
| Nilai & Absensi | ✅ | ✅ *(kelas diampu saja)* |
| Siswa, Kelas, Mapel, Jadwal | ✅ CRUD | 👁️ lihat |
| Guru, Orang Tua, Akun | ✅ CRUD | ⛔ |
| Tagihan & Pembayaran | ✅ | ⛔ |
| Pengumuman | ✅ CRUD | 👁️ lihat |
| Laporan Akademik (PDF) | ✅ | ✅ |
| WhatsApp Gateway | ✅ | ⛔ |

---

## 🛠️ Perintah Artisan Umum

```bash
# Reset database + isi ulang data contoh
php artisan migrate:fresh --seed

# Bersihkan cache (setelah ubah config/view)
php artisan optimize:clear

# Jalankan queue worker (untuk notifikasi WA)
php artisan queue:work --tries=3

# Lihat semua rute terdaftar
php artisan route:list

# Sinkronisasi symlink storage
php artisan storage:link

# Build aset CSS/JS untuk produksi
npm run build
```

---

## 📋 Checklist Setup Awal

- [ ] PHP ≥ 8.2 terinstal
- [ ] Composer terinstal
- [ ] Node.js ≥ 20 terinstal
- [ ] MySQL berjalan & database `siakad_nuja` sudah dibuat
- [ ] `.env` sudah dikonfigurasi (DB, APP_KEY, WAHA)
- [ ] `composer install` selesai
- [ ] `npm install` selesai
- [ ] `php artisan migrate --seed` berhasil
- [ ] `php artisan storage:link` dijalankan
- [ ] `npm run build` atau `npm run dev` berjalan
- [ ] (Opsional) Docker WAHA berjalan di port 3000
- [ ] (Opsional) `php artisan queue:work` berjalan untuk notifikasi WA

---

## 📚 Dokumentasi Teknis

Dokumentasi lengkap tersedia di folder [`docs/`](docs/):

| Dokumen | Isi |
|---------|-----|
| [00-ringkasan-proyek.md](docs/00-ringkasan-proyek.md) | Gambaran umum & daftar dokumen |
| [01-arsitektur-sistem.md](docs/01-arsitektur-sistem.md) | Arsitektur MVC, integrasi WA, struktur folder |
| [02-skema-database.md](docs/02-skema-database.md) | ERD, semua tabel & kolom |
| [03-alur-sistem-dfd.md](docs/03-alur-sistem-dfd.md) | DFD Level 0/1/2 + flowchart |
| [04-modul-fitur.md](docs/04-modul-fitur.md) | Rincian 21+ halaman/fitur |
| [05-chatbot-whatsapp.md](docs/05-chatbot-whatsapp.md) | Spesifikasi FSM chatbot |
| [panduan-waha-docker.md](docs/panduan-waha-docker.md) | Panduan lengkap setup WAHA |

---

## 🐛 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `php artisan` error Class not found | Jalankan `composer dump-autoload` |
| Halaman blank / 500 | Cek `storage/logs/laravel.log` |
| Aset CSS tidak muncul | Jalankan `npm run dev` atau `npm run build` |
| Gambar/storage tidak muncul | Jalankan `php artisan storage:link` |
| Queue tidak jalan | Pastikan `QUEUE_CONNECTION=database` dan jalankan `php artisan queue:work` |
| WAHA tidak menerima pesan | Cek URL webhook menggunakan `host.docker.internal` (bukan `localhost`) |
| Session expired di chatbot | Scan ulang QR dari menu Admin → WhatsApp Gateway |

---

<div align="center">
  Dibangun dengan ❤️ menggunakan Laravel 12, Tailwind CSS v4, dan Alpine.js
</div>
