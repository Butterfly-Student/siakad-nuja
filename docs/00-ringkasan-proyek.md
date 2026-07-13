# SIAKAD Nurul Jadid Karduluk — Ringkasan Proyek

> Dokumen ini adalah titik masuk (entry point) seluruh dokumentasi teknis pembangunan
> **Sistem Informasi Akademik Siswa Berbasis Web Terintegrasi Chatbot WhatsApp**
> di Yayasan Nurul Jadid Karduluk. Diekstrak dari skripsi `KREKSEK_FIXED.docx` (sudah di-ACC dosen).

---

## 1. Deskripsi Singkat

SIAKAD adalah aplikasi web terpusat untuk mengelola data akademik (siswa, guru, kelas,
mata pelajaran, nilai, kehadiran, SPP, pengumuman) yang **terintegrasi dengan chatbot WhatsApp**
untuk notifikasi otomatis dan layanan informasi interaktif dua arah bagi orang tua/wali.

**Masalah yang diselesaikan:** data akademik masih manual & tersebar (shared drive, komputer guru,
arsip fisik) → inkonsistensi data, pelaporan lambat, minim keterlibatan orang tua.

**Solusi:** *single source of truth* berbasis web + kanal WhatsApp proaktif (notifikasi) & interaktif (chatbot).

---

## 2. Tech Stack (sesuai batasan penelitian)

| Lapisan | Teknologi | Catatan |
|---------|-----------|---------|
| Framework backend | **Laravel 12** | Pola MVC, Eloquent ORM, Blade, Migration |
| Bahasa | PHP 8.2+ | Wajib untuk Laravel 12 |
| Database | **MySQL** (8.x) | RDBMS relasional |
| Frontend | **Blade** + AJAX | Server-rendered; interaksi via AJAX request |
| WhatsApp Gateway | **Baileys** (Node.js) / Fonnte / Wablas | REST API di atas sesi WhatsApp; webhook masuk |
| Chatbot | **Rule-based + Finite State Machine (FSM)** | Terhubung langsung ke DB SIAKAD |
| Dev environment | XAMPP (Apache, MySQL, PHP) | Lokal / localhost |
| Testing | Black Box Testing + UAT (ISO/IEC 25010) | 5 dimensi kualitas |
| Autentikasi | Laravel Auth + role-based access | CSRF token aktif |

---

## 3. Aktor Sistem (3 Peran)

| Aktor | Kode DFD | Akses |
|-------|----------|-------|
| **Administrator** | E1 | Kelola seluruh data master, konfigurasi sistem, laporan, keuangan/SPP, pengumuman, log WA |
| **Guru** | E2 | Input nilai, pencatatan kehadiran, rekap kelas, laporan akademik, dashboard mengajar |
| **Orang Tua/Wali** | E4 | Portal pemantauan (nilai, kehadiran, SPP, pengumuman anak) + chatbot WhatsApp |
| WhatsApp Gateway | E5 | Entitas eksternal (bukan pengguna login) — kanal pesan |

> Catatan: Siswa (E3) muncul di Diagram Konteks sebagai penerima layanan informasi,
> namun **tidak login** ke sistem web pada ruang lingkup ini — akses siswa diwakili melalui
> portal Orang Tua/Wali dan chatbot. (Lihat catatan konsistensi di `08-catatan-konsistensi.md`.)

---

## 4. Empat Modul Inti (dari DFD Level 1)

| Proses | Nama | Data Store | Aktor utama |
|--------|------|-----------|-------------|
| **1.1** | Kelola Data Master | D1 | Admin |
| **1.2** | Kelola Nilai & Absensi | D2 | Guru |
| **1.3** | Kelola Pesan WhatsApp (Chatbot) | D3, D2 | WA Gateway ↔ Orang Tua |
| **1.4** | Penyajian Dashboard & Laporan | D1, D2, D4 | Admin |

Data store: **D1** Data Master · **D2** Data Nilai & Absensi · **D3** Data Pesan · **D4** Data Laporan.

---

## 5. Daftar Dokumen

| File | Isi |
|------|-----|
| [`01-arsitektur-sistem.md`](01-arsitektur-sistem.md) | Arsitektur aplikasi, lapisan MVC, integrasi WA, struktur folder Laravel |
| [`02-skema-database.md`](02-skema-database.md) | ERD, seluruh tabel, kolom, tipe, relasi, migration plan |
| [`03-alur-sistem-dfd.md`](03-alur-sistem-dfd.md) | DFD Level 0/1/2 lengkap + flowchart alur proses |
| [`04-modul-fitur.md`](04-modul-fitur.md) | Rincian 21+ halaman/fitur & requirement fungsional |
| [`05-chatbot-whatsapp.md`](05-chatbot-whatsapp.md) | Spesifikasi chatbot: FSM, menu, webhook, notifikasi |
| [`06-rencana-implementasi.md`](06-rencana-implementasi.md) | Roadmap Laravel: migration, model, controller, route, sprint |
| [`07-requirement-nonfungsional.md`](07-requirement-nonfungsional.md) | Keamanan, performa, UAT ISO/IEC 25010 |
| [`08-catatan-konsistensi.md`](08-catatan-konsistensi.md) | Catatan gap desain dari dokumen sumber + keputusan implementasi |

---

## 6. Ruang Lingkup (Batasan)

- Data akademik inti: **siswa, mata pelajaran, kelas, nilai, kehadiran** (+ SPP & pengumuman).
- Pengguna: **Administrator, Guru, Orang Tua/Wali**.
- Chatbot WhatsApp: (a) notifikasi otomatis (ketidakhadiran, nilai, agenda/pengumuman, tagihan);
  (b) layanan respons interaktif dasar (cek nilai/kehadiran/SPP/jadwal/pengumuman via perintah teks).
- Pengembangan: pendekatan **prototyping**, Laravel 12 + MySQL + WhatsApp Gateway.
