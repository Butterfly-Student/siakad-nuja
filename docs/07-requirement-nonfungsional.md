# 07 — Requirement Non-Fungsional

> Sumber: BAB II §2.2.6 & §2.2.8, tahap Uji Coba & Revisi (BAB III). Acuan kualitas: **ISO/IEC 25010**.

---

## 1. Keamanan (Security)

| Aspek | Ketentuan |
|-------|-----------|
| **Autentikasi** | Login berbasis peran; password di-hash **bcrypt**; cek `is_active`. |
| **Otorisasi** | Middleware `role` membatasi akses per peran (admin/guru/wali). |
| **CSRF** | Wajib aktif (bawaan Laravel) di semua form POST — cegah *Cross-Site Request Forgery* (BAB II §2.2.8). |
| **Validasi input** | Semua input divalidasi di boundary (Form Request); nilai 0–100; file bukti hanya JPG/PNG. |
| **SQL Injection** | Gunakan Eloquent/Query Builder (parameterized) — hindari raw query tanpa binding. |
| **XSS** | Blade auto-escape (`{{ }}`); hindari `{!! !!}` untuk data pengguna. |
| **Sesi** | Token sesi diterbitkan setelah login; logout menghapus sesi. |
| **Webhook WA** | Verifikasi token/secret pada endpoint webhook (tanpa auth tapi terproteksi). |
| **Rahasia** | Kredensial gateway di `.env`/tabel konfigurasi, bukan hardcode. |

---

## 2. Performa (Performance Efficiency)

Dari tahap *Revisi Produk Operasional* (BAB III):
- **Optimalisasi query** basis data untuk halaman laporan bervolume besar (index pada kolom filter).
- **Queue** untuk notifikasi WhatsApp — cegah penumpukan saat broadcast massal.
- Notifikasi terkirim cepat (referensi penelitian terkait: < 3 detik).
- Paginasi pada tabel daftar (siswa, log WA, dll.).
- Chatbot: waktu respons target rendah (referensi rule-based ~1,2 detik).

---

## 3. Keandalan (Reliability)

- **Antrean retry** untuk pesan WA gagal + fitur kirim ulang manual (Halaman Log WA).
- Validasi duplikasi data (UNIQUE pada nilai & absensi) mencegah data ganda.
- Timeout sesi chatbot 30 menit → reset state aman.
- Transaksi keuangan tercatat dengan jejak verifikasi (siapa & kapan).

---

## 4. Usability

- Antarmuka **user-friendly** & responsif di berbagai ukuran layar (khusus ortu via *mobile*).
- Chatbot berbasis angka — tanpa perlu mengingat perintah khusus.
- Umpan balik cepat: validasi sisi klien + pesan error jelas.

---

## 5. UAT — ISO/IEC 25010 (Instrumen Pengujian Akhir)

Lima dimensi yang diukur melalui kuesioner terstruktur:

| # | Dimensi | Yang dinilai |
|---|---------|--------------|
| 1 | **Functional Suitability** | Fitur bekerja sesuai kebutuhan (nilai, absensi, notifikasi, chatbot) |
| 2 | **Usability** | Kemudahan penggunaan antarmuka & chatbot |
| 3 | **Reliability** | Keandalan sistem & pengiriman notifikasi |
| 4 | **Performance Efficiency** | Kecepatan respons di beban normal |
| 5 | **User Satisfaction** | Kepuasan pengguna akhir |

Pengujian dilakukan oleh **3 kelompok aktor**: administrator, guru, orang tua/wali.

---

## 6. Pengujian Fungsional (Black Box)

Setiap skenario mencatat: **input → output dihasilkan → output diharapkan → status (sesuai/tidak)**.
Cakupan wajib:
- Login 3 peran (valid/invalid/nonaktif).
- CRUD master data (validasi & duplikasi).
- Hitung nilai akhir + predikat + status kelulusan.
- Absensi + trigger notifikasi WA.
- Alur chatbot semua menu + input tidak dikenal.
- Alur pembayaran SPP (konfirmasi wali → verifikasi admin → kuitansi).
- Broadcast pengumuman & tagihan massal.

---

## 7. Kompatibilitas & Lingkungan

- Berjalan di lingkungan **XAMPP** (Apache, MySQL, PHP) untuk pengembangan/pengujian lokal.
- Akses lintas platform via **browser** (aplikasi web, tanpa instalasi tambahan).
- Produksi: server dengan PHP 8.2+, MySQL 8.x, worker queue aktif, dan konektor WA gateway.
