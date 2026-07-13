# 08 — Catatan Konsistensi & Keputusan Implementasi

> Catatan gap/ambiguitas antara dokumen skripsi (DFD, ERD, deskripsi fitur) dan kebutuhan
> teknis pembangunan. Dokumen skripsi sudah di-ACC, jadi **narasi tidak diubah**; di sini kita
> catat keputusan implementasi agar aplikasi tetap konsisten & benar secara teknis.

---

## 1. Entitas Siswa (E3) tidak login

**Temuan:** Diagram Konteks (Gambar 3.9) menyebut 5 entitas termasuk Siswa (E3), tetapi
DFD Level 1/2 dan seluruh halaman hanya melibatkan Admin, Guru, dan Orang Tua/Wali. Batasan
penelitian juga menyatakan pengguna = Administrator, Guru, Orang Tua/Wali.

**Keputusan:** Siswa **tidak** memiliki akun login pada scope ini. Informasi akademik siswa
diakses melalui **portal Orang Tua/Wali** dan **chatbot WhatsApp**. Tabel `siswa` tetap ada
sebagai data master (bukan tabel akun). *(Jika kelak siswa perlu login, cukup tambah `role='siswa'`
dan relasi `users`↔`siswa`.)*

---

## 2. Data Store D1–D4 → Tabel Fisik

**Temuan:** DFD memakai data store logis (D1 Master, D2 Nilai&Absensi, D3 Pesan, D4 Laporan).

**Keputusan pemetaan:**
- **D1** → `users, siswa, guru, wali, kelas, mata_pelajaran, jadwal, tagihan_spp, pengumuman, konfigurasi`.
- **D2** → `nilai, absensi`.
- **D3** → `chatbot_sessions, chatbot_logs, notifikasi_whatsapp`.
- **D4** → bukan tabel wajib; laporan **di-generate on-demand** dari D1+D2. Boleh dibuat tabel
  cache `laporan` bila perlu menyimpan hasil, tapi tidak diharuskan.

---

## 3. Validasi Nomor Orang Tua (Proses 1.3.2)

**Temuan:** DFD Level 2 (Gambar 3.13) menyebut 1.3.2 mengecek nomor orang tua pada **D3 (Data Pesan)**.
Secara logika, sumber otoritatif nomor orang tua adalah **data master (`wali.no_wa`)**, bukan log pesan.

**Keputusan:** Implementasi mencocokkan nomor pengirim ke **`wali.no_wa` (D1)**. Tabel `chatbot_logs`
(D3) tetap mencatat semua pesan untuk audit. Ini penyimpangan teknis minor yang benar & tidak
mengubah maksud diagram. *(Catat di laporan implementasi bila diperlukan.)*

---

## 4. ERD Skripsi vs Skema Lengkap

**Temuan:** ERD (Gambar 3.15) menyebut 5 entitas inti (Siswa, Guru, Mata Pelajaran, Jadwal, Kelas)
+ Nilai & Absensi. Namun fitur mensyaratkan entitas tambahan: pengguna/role, wali, SPP/pembayaran,
pengumuman, chatbot, notifikasi, konfigurasi.

**Keputusan:** Skema di `02-skema-database.md` adalah **superset** dari ERD skripsi — ERD skripsi
adalah inti akademik, sedangkan tabel pendukung diperlukan agar semua halaman (SPP, chatbot, log WA,
konfigurasi) berfungsi. Tidak ada konflik; hanya pelengkap.

---

## 5. Predikat Nilai (A/B/C/D)

**Temuan:** Flowchart menyebut predikat A/B/C/D dan bobot NH 40% / UTS 30% / UAS 30%, tetapi
ambang batas tiap predikat tidak dinyatakan eksplisit.

**Keputusan (default, dapat dikonfigurasi):** A ≥ 85, B ≥ 75, C ≥ 60, D < 60; KKM default 75
(tuntas/remedial). Nilai ini disimpan di `komponen_nilai`/`konfigurasi` agar dapat diubah admin/guru.

---

## 6. Provider WhatsApp Gateway

**Temuan:** Skripsi menyebut beberapa opsi: **Baileys** (rule-based, Node.js), **Fonnte**, **Wablas**,
**WhatsApp Business API** resmi.

**Keputusan:** Arsitektur *provider-agnostic* — `WhatsappGatewayService` sebagai abstraksi;
URL/token/provider disimpan di `konfigurasi` sehingga bisa ganti provider tanpa ubah kode inti.
Rekomendasi awal: layanan gateway pihak ketiga (Fonnte/Wablas) untuk kesederhanaan, atau Baileys
bila ingin kendali penuh.

---

## 7. Status Absensi

**Temuan:** Dua penyebutan status: flowchart absensi (Hadir/Sakit/Izin/Alpha) dan halaman
pencatatan (Hadir/Absen/Izin/Sakit).

**Keputusan:** Gunakan **Hadir/Izin/Sakit/Alpha** (enum `absensi.status`). "Absen" = "Alpha".
Notifikasi WA dipicu pada status **Alpha**.

---

## 8. Duplikasi Kalimat di Sumber (sudah diperbaiki)

Pada proses perapian dokumen sebelumnya, satu kalimat ganda di deskripsi Halaman Login telah
dibersihkan. Tidak berdampak pada ekstraksi teknis ini.

---

## Ringkasan Keputusan
| # | Isu | Keputusan |
|---|-----|-----------|
| 1 | Siswa login? | Tidak; via portal wali & chatbot |
| 2 | D4 Laporan | Generate on-demand (tabel opsional) |
| 3 | Validasi no. WA | Cocokkan ke `wali.no_wa`, log ke D3 |
| 4 | ERD vs skema | Skema = superset (inti + pendukung) |
| 5 | Ambang predikat | A≥85, B≥75, C≥60, D<60; KKM 75 (configurable) |
| 6 | Gateway | Provider-agnostic via service + konfigurasi |
| 7 | Status absensi | Hadir/Izin/Sakit/Alpha; notif saat Alpha |
