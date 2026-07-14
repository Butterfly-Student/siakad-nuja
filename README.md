# SIAKAD NUJA — Sistem Informasi Akademik

Aplikasi Sistem Informasi Akademik sekolah (jenjang SMP) untuk mengelola **siswa, guru, kelas, mata pelajaran, jadwal, nilai, absensi, orang tua, dan pengumuman**. Dilengkapi autentikasi & otorisasi peran **Admin** dan **Guru**, landing page publik yang modern, serta antarmuka responsif (mobile-first) dengan mode gelap.

Dibangun dengan **Laravel 12 · PHP 8.2+ · MySQL · Tailwind CSS v4 · Alpine.js · Vite**.

---

## ✨ Fitur Utama

- **Landing page publik** di `/` — modern, beranimasi, langsung mengarah ke halaman login.
- **Autentikasi & Otorisasi** peran Admin/Guru berbasis middleware & Policy (tanpa paket pihak ketiga).
  - Akun nonaktif (`is_active = false`) diblokir saat login.
  - Registrasi publik dinonaktifkan — akun dibuat oleh Admin.
- **Manajemen data master** (Admin): siswa, guru, kelas, mapel, jadwal, orang tua, dan akun pengguna.
- **Nilai otomatis** — `nilai_akhir` (harian 30% · UTS 30% · UAS 40%) & `predikat` dihitung otomatis.
- **Absensi massal** — pilih jadwal + tanggal, isi kehadiran seluruh siswa satu kelas sekaligus.
- **Scoping guru** — guru hanya melihat/mengisi nilai & absensi untuk kelas yang diampu atau diwalikan.
- **Dasbor role-aware** — statistik global untuk Admin; jadwal mengajar hari ini untuk Guru.
- **UI komponen reusable** (Blade) + tabel responsif (tabel di desktop, kartu di mobile) + dark mode.

---

## 🧩 Persyaratan

| Kebutuhan | Versi |
|-----------|-------|
| PHP       | ≥ 8.2 (disarankan 8.4) dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo` |
| Composer  | 2.x |
| Node.js   | ≥ 20 (disarankan 22+) |
| MySQL / MariaDB | 8.x / 10.4+ |

> Di Windows, [Laragon](https://laragon.org/) sudah menyediakan PHP, MySQL, dan Composer sekaligus.

---

## 🚀 Instalasi & Setup

```bash
# 1. Clone & masuk ke folder proyek
git clone <url-repo> siakad-nuja
cd siakad-nuja

# 2. Install dependency PHP & JavaScript
composer install
npm install

# 3. Siapkan file environment
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
```

Ubah kredensial database di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad_nuja
DB_USERNAME=root
DB_PASSWORD=
```

Buat database kosong terlebih dahulu (mis. `siakad_nuja`), lalu:

```bash
# 4. Migrasi + data contoh (seeder)
php artisan migrate:fresh --seed

# 5. Symlink storage (untuk foto siswa)
php artisan storage:link

# 6. Build aset front-end
npm run build
```

---

## ▶️ Menjalankan Aplikasi

**Mode pengembangan** (jalankan di dua terminal):

```bash
php artisan serve      # backend  → http://127.0.0.1:8000
npm run dev            # Vite dev server (hot reload aset)
```

**Mode produksi / preview build:**

```bash
npm run build          # kompilasi aset ke public/build
php artisan serve
```

Buka `http://127.0.0.1:8000` — Anda akan disambut landing page, lalu klik **Masuk**.

---

## 🔑 Kredensial Default (dari seeder)

| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@siakadnuja.sch.id` | `password` |
| Guru  | `guru1@siakadnuja.sch.id` … `guru10@siakadnuja.sch.id` | `password` |

Data contoh mencakup ±168 siswa, 10 guru, 6 kelas, 10 mapel, 180 jadwal, serta nilai & absensi.

---

## 🗂️ Struktur Penting

```
app/
├─ Http/
│  ├─ Controllers/        # 13 controller (master data, nilai, absensi, user, profil, auth)
│  ├─ Middleware/         # EnsureUserHasRole (alias: role)
│  └─ Requests/           # FormRequest validasi tiap modul
├─ Models/                # Eloquent: User, Guru, Siswa, Kelas, dst.
└─ Policies/              # NilaiPolicy, AbsensiPolicy (scoping guru)

resources/
├─ css/app.css            # Tailwind v4 + design token + animasi landing
├─ js/app.js              # Alpine.js (persist, intersect)
└─ views/
   ├─ landing/            # Partial landing page (navbar, hero, features, dst.)
   ├─ layouts/            # app.blade.php + partials/sidebar
   ├─ components/         # Komponen Blade reusable (x-button, x-card, x-table, x-form.*, …)
   └─ <modul>/            # index / create / edit / show / _form tiap modul

database/
├─ factories/             # Factory data contoh
├─ migrations/            # Skema tabel
└─ seeders/               # DatabaseSeeder (data awal + kredensial)

docs/
└─ view-styleguide.md     # Panduan membangun view dengan komponen
```

---

## 👥 Peran & Hak Akses

| Modul | Admin | Guru |
|-------|:-----:|:----:|
| Dashboard | ✅ | ✅ (jadwal hari ini) |
| Nilai & Absensi | ✅ | ✅ *(hanya kelas yang diampu/diwalikan)* |
| Siswa, Kelas, Mapel, Jadwal | ✅ CRUD | 👁️ lihat saja |
| Guru, Orang Tua, Pengumuman, Akun | ✅ CRUD | ⛔ 403 |
| Profil & ganti password | ✅ | ✅ |

Akses guru ke URL admin secara langsung akan ditolak (HTTP 403) oleh middleware `role`.

---

## 🛠️ Perintah yang Sering Dipakai

```bash
php artisan migrate:fresh --seed   # reset ulang database + data contoh
php artisan route:list             # daftar semua rute
php artisan view:clear             # bersihkan cache view Blade
php artisan optimize:clear         # bersihkan seluruh cache
npm run dev                        # aset dengan hot-reload
npm run build                      # build aset produksi
```

---

## 📝 Catatan

- Modul WhatsApp/chatbot **belum** termasuk dalam rilis ini (tabel `chatbot_*` disiapkan untuk pengembangan lanjutan).
- Foto siswa disimpan di `storage/app/public/siswa` dan diakses via symlink `public/storage` — pastikan `php artisan storage:link` sudah dijalankan.

---

Dibangun dengan ❤️ menggunakan Laravel, Tailwind CSS, dan Alpine.js.
