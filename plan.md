# Rencana: SIAKAD NUJA — CRUD Fungsional, Auth/Authorization, UI Tailwind Modern

## Context

SIAKAD NUJA adalah aplikasi Sistem Informasi Akademik sekolah (Laravel 12, PHP 8.2, MySQL). Hasil eksplorasi menunjukkan fondasi sudah ada tetapi **belum siap dipakai**:

- **CRUD sudah ada** — 8 resource controller (Siswa, Guru, Kelas, MataPelajaran, JadwalPelajaran, Nilai, Absensi, OrangTua, Pengumuman) sudah lengkap 7 method + view (index/create/edit/show/_form). Jadi ini **penguatan**, bukan bikin dari nol.
- **Authorization TIDAK ADA** — kolom `role` ada di tabel `users` tapi tidak ada gate/policy/middleware. Semua user login bisa akses semua modul.
- **Seeder & factory RUSAK** — `DatabaseSeeder` + `UserFactory` masih bawaan Laravel (pakai kolom `name`/`email_verified_at` yang tidak ada; tabel butuh `nama` + `role` NOT NULL). Akibatnya `db:seed` gagal dan **tidak ada cara login** setelah fresh install. Tidak ada data contoh.
- **UI pakai Bootstrap 5 CDN** meski Tailwind 4 + Vite sudah terpasang tapi tidak dipakai (layout tak pernah `@vite`). Sidebar tidak mobile-friendly, tidak role-aware.

**Tujuan:** CRUD berfungsi baik & aman, autentikasi + otorisasi peran **Admin & Guru**, dan UI/UX modern, responsive, mobile-first berbasis **Tailwind CSS v4 + Alpine.js**. WhatsApp/chatbot dikecualikan (planning terpisah).

**Keputusan yang sudah dikonfirmasi user:**
1. **Migrasi ke Tailwind 4** (rewrite view, `@vite`, Alpine, dark mode, komponen reusable, mobile-first).
2. **Peran: Admin + Guru saja** (tanpa portal siswa/orang tua).
3. **Wewenang Guru: input/edit Nilai & Absensi + lihat (read-only) data siswa/kelas/mapel/jadwal.** Master data (siswa, guru, kelas, mapel, jadwal, orang tua, user) hanya Admin. Guru idealnya dibatasi ke kelas yang diampu (`jadwal_pelajaran.guru_id`) atau diwalikan (`kelas.wali_kelas_id`).

---

## Fase 1 — Autentikasi & Otorisasi (fondasi keamanan)

Pendekatan **native Laravel 12** tanpa paket berat (tidak perlu spatie).

### 1.1 Helper peran pada User
`app/Models/User.php`: tambah konstanta & method `isAdmin(): bool`, `isGuru(): bool`, dan scope opsional. Manfaatkan relasi `guru()` yang sudah ada untuk scoping.

### 1.2 Middleware peran
- Buat `app/Http/Middleware/EnsureUserHasRole.php` (menerima parameter role, mis. `role:admin`).
- Daftarkan alias di `bootstrap/app.php` (closure `withMiddleware` saat ini kosong):
  ```php
  $middleware->alias(['role' => \App\Http\Middleware\EnsureUserHasRole::class]);
  ```

### 1.3 Pengelompokan route (`routes/web.php`)
Pecah grup `auth` menjadi:
- **Shared (admin + guru):** `dashboard`, `nilai.*`, `absensi.*`, plus **read-only** untuk `siswa`, `kelas`, `mata-pelajaran`, `jadwal`, `pengumuman` (index/show).
- **Admin-only** (`->middleware('role:admin')`): full CRUD `guru`, `kelas`, `mata-pelajaran`, `siswa`, `jadwal`, `orang-tua`, `pengumuman`, dan modul **User management** baru. Untuk modul yang dibagi read/write, gunakan `Route::resource(...)->only([...])` untuk memisah method create/store/edit/update/destroy (admin) dari index/show (shared).

### 1.4 Policy untuk scoping Guru
- `NilaiPolicy` & `AbsensiPolicy`: Guru hanya boleh create/update untuk kelas/mapel yang ia ampu (cek lewat `JadwalPelajaran` `guru_id` = `auth()->user()->guru->id`) atau kelas yang ia walikan (`kelas.wali_kelas_id`). Admin boleh semua.
- Panggil `$this->authorize(...)` di `NilaiController` & `AbsensiController` (store/update/destroy), dan filter query `index` agar Guru hanya melihat data kelasnya.
- Registrasi otomatis via konvensi Laravel 12 (Policy discovery) atau `Gate::policy()` di `AppServiceProvider`.

### 1.5 Perbaikan Auth flow (`app/Http/Controllers/Auth/LoginController.php`)
- Cek `is_active` saat login — blokir user nonaktif dengan pesan.
- Redirect setelah login berdasarkan peran (keduanya ke `dashboard`, konten dashboard yang berbeda).
- Registrasi publik tetap **dinonaktifkan** (akun dibuat Admin).

### 1.6 Modul User Management (Admin-only, baru)
- `UserController` (resource) + `app/Http/Requests/User{Store,Update}Request`.
- Admin membuat akun Guru: buat `User` (role=guru) + record `Guru` terkait (nip, nama_lengkap, jabatan, no_hp) dalam satu transaksi. Fitur reset password & toggle `is_active`.
- View index/create/edit dengan komponen form Tailwind.
- Halaman **Profil / Ganti Password** untuk semua user (`ProfileController`).

---

## Fase 2 — Setup UI Tailwind 4 + Alpine (fondasi tampilan)

### 2.1 Build pipeline
- `resources/js/app.js`: import & start **Alpine.js** (tambah dependency `alpinejs`). Hapus dependensi Bootstrap JS.
- `resources/css/app.css`: definisikan design tokens via Tailwind v4 `@theme` (palet warna brand — mis. indigo/slate sebagai primary, warna semantik success/warning/danger, radius, font). Aktifkan **dark mode** (strategi `class`).
- Ikon: pakai **inline SVG via komponen Blade** (`<x-icon name="..."/>`) atau set Heroicons — hindari CDN icon font. Rekomendasi: komponen `x-icon` kecil berisi SVG yang dipakai (sidebar, aksi tabel).

### 2.2 Layout inti (rewrite `resources/views/layouts/app.blade.php`)
- Ganti Bootstrap CDN → `@vite(['resources/css/app.css','resources/js/app.js'])`.
- **Mobile-first**: sidebar sebagai **drawer/offcanvas** yang di-toggle Alpine di layar kecil, **persisten** di desktop (`lg:` breakpoint). Topbar dengan tombol hamburger, judul halaman, menu user (dropdown Alpine: Profil, Logout), toggle dark mode.
- **Nav role-aware**: link master data (Guru, Kelas, Mapel, Jadwal, Orang Tua, User) hanya tampil untuk Admin; Nilai/Absensi/Siswa(view)/Dashboard untuk semua. Gunakan `@if(auth()->user()->isAdmin())`.
- Flash message & error divalidasi jadi komponen alert Tailwind (auto-dismiss via Alpine).

### 2.3 Pustaka komponen Blade reusable (`resources/views/components/`)
Bangun sekali, pakai di semua modul:
- `x-app-layout` (wrapper), `x-page-header` (judul + breadcrumb + tombol aksi), `x-card`.
- Form: `x-form.input`, `x-form.select`, `x-form.textarea`, `x-form.date`, `x-form.file`, `x-form.error`, `x-form.label` — otomatis menampilkan error validasi & old value.
- `x-button` (varian primary/secondary/danger/ghost + ukuran), `x-badge` (status/predikat), `x-table` + `x-table.row` helper, `x-stat-card`, `x-empty-state`, `x-modal` (Alpine), `x-confirm` (dialog hapus Alpine, ganti `confirm()` bawaan), `x-pagination` (styling Laravel paginator Tailwind — set `Paginator::useTailwind()` bila perlu).

### 2.4 Pola tabel mobile-first
Tabel index: `hidden md:table` untuk layar besar; di mobile render sebagai **daftar kartu** (stacked card per baris dengan label-value). Aksi (lihat/edit/hapus) sebagai tombol ikon dengan `x-confirm` untuk delete.

---

## Fase 3 — Penguatan CRUD (semua controller)

### 3.1 FormRequest untuk validasi
Ekstrak validasi inline controller ke `app/Http/Requests/` (mis. `SiswaStoreRequest`, `SiswaUpdateRequest`, dst.) untuk 9 modul + User. Termasuk:
- **Unique dengan ignore-on-update**: `nis` (siswa), `nip` (guru), `kode_mapel` (mapel), `email` (user) — pakai `Rule::unique(...)->ignore($id)`.
- Aturan enum untuk `jenis_kelamin`, `status`, `hari`, `semester`, `hubungan`, `target_role`, `status` absensi.
- `authorize()` mengembalikan hasil policy jika relevan.

### 3.2 Dropdown relasi yang benar
Pastikan create/edit mengirim opsi relasi ke view:
- **Jadwal**: select `kelas` + `mapel` + `guru` + `hari` + jam.
- **Nilai**: select `siswa` (difilter per kelas), `mapel`, `kelas`, `semester`, `tahun_ajaran`.
- **Absensi**: pilih `jadwal` (kelas+mapel) + `tanggal`, lalu daftar siswa.
- **Siswa**: select `kelas`. **Kelas**: select `wali_kelas` (guru). **OrangTua**: select `siswa`.

### 3.3 Fitur khusus modul
- **Siswa**: upload `foto` (simpan `storage/app/public`, `php artisan storage:link`), tampilkan avatar/placeholder.
- **Nilai**: **auto-hitung** `nilai_akhir` dari `nilai_harian/uts/uas` (bobot yang wajar) dan `predikat` dari `nilai_akhir` vs `kkm` mapel. Boleh dihitung di model (accessor/mutator) atau di FormRequest/Controller.
- **Absensi (perbaikan UX)**: modul ini hanya punya index+_form. Buat **entri absensi massal**: pilih jadwal + tanggal → tampilkan **semua siswa di kelas itu** dengan radio status (Hadir/Izin/Sakit/Alpa) dalam satu form; simpan sekaligus (upsert per siswa+jadwal+tanggal). Ini sesuai cara absensi nyata.
- **Guru scoping**: `NilaiController@index` & `AbsensiController@index` memfilter ke kelas guru bila `isGuru()`.

### 3.4 Konsistensi
- Flash message sukses/gagal seragam, redirect yang benar, eager-loading untuk hindari N+1, pagination di semua index, empty-state bila kosong.
- **Perhatikan quirk parameter route**: `kelas` → binding `{kela}` (singularisasi Laravel), `mata-pelajaran` → `mataPelajaran`, `orang-tua` → `orangTua`. Verifikasi signature method controller cocok saat refactor.

---

## Fase 4 — Factories & Seeders lengkap

### 4.1 Perbaiki factory bawaan
`database/factories/UserFactory.php`: ganti `name`→`nama`, hapus `email_verified_at`, tambah `role`, `no_hp`, `is_active`.

### 4.2 Factory domain (baru)
`GuruFactory`, `KelasFactory`, `MataPelajaranFactory`, `SiswaFactory`, `JadwalPelajaranFactory`, `NilaiFactory`, `AbsensiFactory`, `OrangTuaFactory`, `PengumumanFactory` — data realistis Bahasa Indonesia.

### 4.3 Seeder berurutan (hormati FK)
Rewrite `DatabaseSeeder` untuk `$this->call([...])` dengan urutan: User/Guru → MataPelajaran → Kelas (assign wali_kelas) → Siswa → OrangTua → JadwalPelajaran → Nilai → Absensi → Pengumuman.

**Kredensial diketahui untuk login:**
- Admin: `admin@siakadnuja.sch.id` / `password` (role=admin).
- Beberapa Guru: `guru1@...` dst / `password` (role=guru, masing-masing tertaut record `Guru`).

**Volume & konsistensi contoh (pilih jenjang konsisten, mis. SMP):**
- 1 Admin + ~10 Guru (tertaut User).
- ~10 Mapel (Matematika, B. Indonesia, B. Inggris, IPA, IPS, PKN, PAI, Penjas, Seni Budaya, TIK) dengan KKM (mis. 70–75).
- ~6 Kelas (7A, 7B, 8A, 8B, 9A, 9B), tiap kelas punya wali_kelas.
- ~25–30 Siswa per kelas (≈150–180 total), NIS unik, tersebar sesuai kapasitas.
- 1–2 OrangTua per siswa (satu `is_kontak_utama`).
- **Jadwal koheren**: Senin–Jumat, jam_ke 1–8, tanpa bentrok guru/ruang/kelas.
- Nilai per siswa per mapel untuk semester aktif, `nilai_akhir` & `predikat` terhitung benar.
- Absensi beberapa hari terakhir tertaut jadwal.
- Beberapa Pengumuman aktif (target_role bervariasi).

---

## Fase 5 — Rebuild view per modul (Tailwind)

Untuk tiap modul, susun ulang menggunakan komponen Fase 2:
- **Dashboard**: role-specific. Admin = statistik global (total siswa/guru/kelas/mapel, pengumuman terbaru, grafik ringkas). Guru = kelas yang diampu/diwalikan, jadwal hari ini, pintasan input nilai/absensi.
- **Index** tiap modul: page-header + tabel responsive (kartu di mobile) + search/filter sederhana + pagination.
- **Create/Edit**: form pakai komponen `x-form.*`, layout dua kolom di desktop, satu kolom di mobile.
- **Show**: kartu detail + relasi (mis. Siswa → orang tua, nilai, absensi).
- Tombol aksi & link disembunyikan sesuai peran (Guru tak melihat tombol edit di master data).

---

## File-file kunci

**Baru:**
- `app/Http/Middleware/EnsureUserHasRole.php`
- `app/Policies/{NilaiPolicy,AbsensiPolicy}.php`
- `app/Http/Requests/**` (Store/Update tiap modul + User)
- `app/Http/Controllers/{UserController,ProfileController}.php`
- `database/factories/*` (9 factory domain)
- `database/seeders/*` (seeder per modul)
- `resources/views/components/**` (pustaka komponen)
- View modul User & Profil; view absensi create/edit/show (entri massal)

**Diubah:**
- `bootstrap/app.php` (alias middleware)
- `routes/web.php` (grup admin-only vs shared)
- `app/Models/User.php` (isAdmin/isGuru)
- `app/Http/Controllers/{Nilai,Absensi}Controller.php` (authorize + scoping) + semua controller (pakai FormRequest)
- `resources/views/layouts/app.blade.php` (rewrite Tailwind + Alpine + @vite)
- `resources/css/app.css`, `resources/js/app.js`, `package.json` (alpinejs)
- `database/factories/UserFactory.php`, `database/seeders/DatabaseSeeder.php`
- Semua view modul (rewrite Tailwind)

---

## Verifikasi (end-to-end)

1. `npm install && npm run build` (atau `npm run dev`) — pastikan Vite/Tailwind terkompilasi tanpa error.
2. `php artisan migrate:fresh --seed` — harus sukses tanpa error FK/kolom.
3. `php artisan storage:link` — untuk foto siswa.
4. **Login sebagai Admin** → cek akses penuh semua modul; CRUD tiap modul (create/edit/delete) berfungsi; user management membuat akun guru.
5. **Login sebagai Guru** → verifikasi: tidak bisa akses/ubah master data (403), bisa input/edit Nilai & Absensi hanya untuk kelas yang diampu/diwalikan, hanya melihat data siswa/kelas/mapel/jadwal (read-only). Coba akses URL admin langsung → harus 403.
6. **Absensi massal**: pilih jadwal+tanggal, isi status semua siswa, simpan, cek tersimpan benar.
7. **Nilai**: input harian/uts/uas → `nilai_akhir` & `predikat` terhitung otomatis sesuai KKM.
8. **Responsive/mobile**: buka di DevTools device mode — sidebar jadi drawer, tabel jadi kartu, form satu kolom; uji dark mode toggle.
9. Cek `is_active=false` memblokir login.
