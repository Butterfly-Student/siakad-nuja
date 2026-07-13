# 02 — Skema Database

> Diturunkan dari ERD (Gambar 3.15), DFD Level 2, dan deskripsi seluruh halaman/fitur
> pada BAB III `KREKSEK_FIXED.docx`. Target: **MySQL 8.x** + **Laravel 12 Migration/Eloquent**.
>
> ERD sumber menyebut 5 entitas inti (Siswa, Guru, Mata Pelajaran, Jadwal, Kelas) plus
> entitas Nilai & Absensi. Skema di bawah **melengkapi** entitas tsebут dengan tabel pendukung
> yang tersirat dari fitur (pengguna/role, wali, SPP/pembayaran, pengumuman, chatbot, notifikasi, konfigurasi).

---

## 1. Daftar Tabel (ringkas)

| # | Tabel | Kelompok | Keterangan |
|---|-------|----------|------------|
| 1 | `users` | Auth | Akun login semua peran |
| 2 | `roles` | Auth | admin, guru, wali (opsional bila pakai kolom `role`) |
| 3 | `tahun_ajaran` | Master | Tahun ajaran + semester aktif |
| 4 | `kelas` | Master | Rombongan belajar |
| 5 | `mata_pelajaran` | Master | Mapel |
| 6 | `guru` | Master | Profil guru (1–1 ke users) |
| 7 | `siswa` | Master | Profil siswa |
| 8 | `wali` | Master | Orang tua/wali (1–1 ke users) |
| 9 | `siswa_wali` | Pivot | Relasi banyak siswa ↔ banyak wali |
| 10 | `guru_mapel` | Pivot | Guru mengampu banyak mapel |
| 11 | `jadwal` | Master | Jadwal mengajar (guru+mapel+kelas) |
| 12 | `nilai` | Transaksi | Nilai per siswa/mapel/semester |
| 13 | `komponen_nilai` | Konfig | Bobot Harian/UTS/UAS |
| 14 | `absensi` | Transaksi | Kehadiran harian per siswa |
| 15 | `pengumuman` | Konten | Pengumuman sekolah |
| 16 | `pengumuman_target` | Pivot | Target penerima pengumuman |
| 17 | `tagihan_spp` | Keuangan | Tagihan SPP per siswa/bulan |
| 18 | `pembayaran_spp` | Keuangan | Transaksi & konfirmasi pembayaran |
| 19 | `notifikasi_whatsapp` | WA | Log notifikasi otomatis keluar |
| 20 | `chatbot_sessions` | Chatbot | State FSM percakapan |
| 21 | `chatbot_logs` | Chatbot | Log semua pesan masuk/keluar chatbot |
| 22 | `konfigurasi` | Sistem | Pengaturan global (key-value) |

---

## 2. ERD Ringkas (relasi utama)

```
tahun_ajaran ──1:N── kelas ──1:N── siswa ──N:M── wali        (via siswa_wali)
                        │             │
                        │             ├──1:N── nilai ──N:1── mata_pelajaran
                        │             ├──1:N── absensi
                        │             └──1:N── tagihan_spp ──1:N── pembayaran_spp
                        │
guru ──N:M── mata_pelajaran (via guru_mapel)
 │
 └──1:N── jadwal ──N:1── kelas, mata_pelajaran, tahun_ajaran

users ──1:1── guru | wali        (polymorphic-ish via role + relasi)
wali ──1:N── chatbot_sessions, chatbot_logs, notifikasi_whatsapp
```

---

## 3. Definisi Tabel Lengkap

### 3.1 `users`
Akun login untuk **semua** peran.

| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(150) | Nama tampil |
| username | VARCHAR(50) UNIQUE | Login admin/guru |
| email | VARCHAR(150) UNIQUE NULL | |
| no_hp | VARCHAR(20) UNIQUE NULL | Login wali via no. HP |
| password | VARCHAR(255) | bcrypt hash |
| role | ENUM('admin','guru','wali') | Peran akses |
| is_active | BOOLEAN default 1 | Status keaktifan akun |
| remember_token | VARCHAR(100) NULL | |
| timestamps | | |

### 3.2 `tahun_ajaran`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| nama | VARCHAR(20) | mis. "2025/2026" |
| semester | ENUM('ganjil','genap') | |
| is_aktif | BOOLEAN default 0 | Hanya satu aktif |
| timestamps | | |

### 3.3 `kelas`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| nama | VARCHAR(50) | mis. "VII-A" |
| tingkat | VARCHAR(10) | mis. "VII" |
| wali_kelas_id | BIGINT FK → guru.id NULL | |
| tahun_ajaran_id | BIGINT FK → tahun_ajaran.id | |
| timestamps | | |

### 3.4 `mata_pelajaran`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| kode | VARCHAR(20) UNIQUE | |
| nama | VARCHAR(100) | |
| tingkat | VARCHAR(10) NULL | |
| jam_per_minggu | TINYINT default 2 | |
| timestamps | | |

### 3.5 `guru`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id UNIQUE | |
| nip | VARCHAR(30) UNIQUE NULL | |
| nama | VARCHAR(150) | |
| email | VARCHAR(150) NULL | |
| no_hp | VARCHAR(20) NULL | |
| jenis_kelamin | ENUM('L','P') NULL | |
| status | ENUM('aktif','nonaktif') default 'aktif' | |
| timestamps | | |

### 3.6 `siswa`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| nis | VARCHAR(30) UNIQUE | Nomor induk siswa |
| nisn | VARCHAR(30) UNIQUE NULL | |
| nama | VARCHAR(150) | |
| jenis_kelamin | ENUM('L','P') | |
| tanggal_lahir | DATE NULL | |
| kelas_id | BIGINT FK → kelas.id | |
| tahun_masuk | YEAR NULL | |
| status | ENUM('aktif','lulus','pindah','keluar') default 'aktif' | |
| timestamps | | |

### 3.7 `wali`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| user_id | BIGINT FK → users.id UNIQUE | |
| nama | VARCHAR(150) | |
| nik | VARCHAR(20) NULL | |
| no_wa | VARCHAR(20) | **Nomor WhatsApp** (kunci notifikasi & chatbot) |
| alamat | TEXT NULL | |
| timestamps | | |

> **Penting:** `wali.no_wa` adalah kunci untuk mencocokkan pesan WhatsApp masuk → identitas wali → siswa.

### 3.8 `siswa_wali` (pivot N:M)
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| siswa_id | BIGINT FK → siswa.id | |
| wali_id | BIGINT FK → wali.id | |
| hubungan | ENUM('ayah','ibu','wali') | |
| UNIQUE(siswa_id, wali_id) | | |

### 3.9 `guru_mapel` (pivot N:M)
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| guru_id | BIGINT FK → guru.id | |
| mata_pelajaran_id | BIGINT FK → mata_pelajaran.id | |
| UNIQUE(guru_id, mata_pelajaran_id) | | |

### 3.10 `jadwal`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| kelas_id | BIGINT FK → kelas.id | |
| mata_pelajaran_id | BIGINT FK → mata_pelajaran.id | |
| guru_id | BIGINT FK → guru.id | |
| tahun_ajaran_id | BIGINT FK → tahun_ajaran.id | |
| hari | ENUM('senin'..'sabtu') | |
| jam_mulai | TIME | |
| jam_selesai | TIME | |
| timestamps | | |

### 3.11 `nilai`
Nilai per siswa per mapel per semester. Nilai akhir dihitung otomatis (lihat §5).

| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| siswa_id | BIGINT FK → siswa.id | |
| mata_pelajaran_id | BIGINT FK → mata_pelajaran.id | |
| guru_id | BIGINT FK → guru.id | Penginput |
| tahun_ajaran_id | BIGINT FK → tahun_ajaran.id | |
| nilai_harian | DECIMAL(5,2) NULL | 0–100 |
| nilai_uts | DECIMAL(5,2) NULL | 0–100 |
| nilai_uas | DECIMAL(5,2) NULL | 0–100 |
| nilai_akhir | DECIMAL(5,2) NULL | Hasil hitung |
| predikat | ENUM('A','B','C','D') NULL | |
| status_kelulusan | ENUM('tuntas','remedial') NULL | |
| UNIQUE(siswa_id, mata_pelajaran_id, tahun_ajaran_id) | | Cegah duplikasi |
| timestamps | | |

### 3.12 `komponen_nilai`
Bobot komponen penilaian (default & dapat diubah guru/admin).

| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| bobot_harian | TINYINT default 40 | % |
| bobot_uts | TINYINT default 30 | % |
| bobot_uas | TINYINT default 30 | % |
| kkm | TINYINT default 75 | Kriteria ketuntasan |
| mata_pelajaran_id | BIGINT FK NULL | NULL = global default |
| timestamps | | |

> Bobot default dari flowchart: **NH × 40% + UTS × 30% + UAS × 30%**.

### 3.13 `absensi`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| siswa_id | BIGINT FK → siswa.id | |
| kelas_id | BIGINT FK → kelas.id | |
| guru_id | BIGINT FK → guru.id | Pencatat |
| tanggal | DATE | |
| status | ENUM('hadir','izin','sakit','alpha') | |
| keterangan | VARCHAR(255) NULL | |
| UNIQUE(siswa_id, tanggal) | | Cegah duplikasi harian |
| timestamps | | |

### 3.14 `pengumuman`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| judul | VARCHAR(200) | |
| isi | TEXT | |
| kategori | VARCHAR(50) NULL | |
| dibuat_oleh | BIGINT FK → users.id | admin/guru |
| tanggal_publikasi | DATE | |
| target | ENUM('semua','kelas','wali_tertentu') | |
| kirim_wa | BOOLEAN default 0 | Kirim notifikasi WA? |
| status | ENUM('draft','publish') default 'draft' | |
| timestamps | | |

### 3.15 `pengumuman_target` (pivot)
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| pengumuman_id | BIGINT FK → pengumuman.id | |
| kelas_id | BIGINT FK → kelas.id NULL | jika target = kelas |
| wali_id | BIGINT FK → wali.id NULL | jika target = wali_tertentu |
| is_read | BOOLEAN default 0 | status baca portal wali |
| timestamps | | |

### 3.16 `tagihan_spp`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| siswa_id | BIGINT FK → siswa.id | |
| tahun_ajaran_id | BIGINT FK → tahun_ajaran.id | |
| bulan | VARCHAR(20) | mis. "Januari 2026" |
| nominal | DECIMAL(12,2) | |
| jatuh_tempo | DATE NULL | |
| status | ENUM('belum_bayar','menunggu_verifikasi','lunas') default 'belum_bayar' | |
| timestamps | | |

### 3.17 `pembayaran_spp`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| tagihan_spp_id | BIGINT FK → tagihan_spp.id | |
| tanggal_bayar | DATE | |
| metode | ENUM('cash','transfer') | |
| nominal_bayar | DECIMAL(12,2) | |
| bank_pengirim | VARCHAR(50) NULL | |
| nama_pengirim | VARCHAR(150) NULL | |
| bukti_transfer | VARCHAR(255) NULL | path file JPG/PNG |
| status_verifikasi | ENUM('menunggu','disetujui','ditolak') default 'menunggu' | |
| alasan_tolak | VARCHAR(255) NULL | |
| diverifikasi_oleh | BIGINT FK → users.id NULL | admin |
| timestamps | | |

### 3.18 `notifikasi_whatsapp`
Log notifikasi **otomatis keluar** (absensi, nilai, pengumuman, tagihan).

| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| wali_id | BIGINT FK → wali.id NULL | |
| siswa_id | BIGINT FK → siswa.id NULL | |
| no_tujuan | VARCHAR(20) | |
| jenis | ENUM('absensi','nilai','pengumuman','tagihan','umum') | |
| isi_pesan | TEXT | |
| status | ENUM('pending','terkirim','gagal') default 'pending' | |
| dikirim_pada | TIMESTAMP NULL | |
| timestamps | | |

### 3.19 `chatbot_sessions`
State FSM percakapan (batas 30 menit — lihat flowchart chatbot).

| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| no_hp | VARCHAR(20) UNIQUE | |
| wali_id | BIGINT FK → wali.id NULL | |
| state | VARCHAR(50) default 'MENU_UTAMA' | mis. MENU_UTAMA, MENU_NILAI |
| context | JSON NULL | data sementara sesi |
| last_activity | TIMESTAMP | untuk timeout 30 mnt |
| timestamps | | |

### 3.20 `chatbot_logs`
| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| no_hp | VARCHAR(20) | |
| wali_id | BIGINT FK → wali.id NULL | |
| arah | ENUM('masuk','keluar') | |
| pesan | TEXT | |
| intent | VARCHAR(50) NULL | nilai/absensi/spp/jadwal/pengumuman |
| created_at | TIMESTAMP | |

### 3.21 `konfigurasi`
Pengaturan global (key-value) — info sekolah, template pesan WA, koneksi gateway.

| Kolom | Tipe | Ket. |
|-------|------|------|
| id | BIGINT PK | |
| key | VARCHAR(100) UNIQUE | mis. `wa_gateway_url`, `template_absensi` |
| value | TEXT NULL | |
| timestamps | | |

---

## 4. Urutan Migration (hormati foreign key)

```
1. users
2. tahun_ajaran
3. mata_pelajaran
4. guru               (FK users)
5. kelas              (FK guru wali_kelas, tahun_ajaran)
6. siswa              (FK kelas)
7. wali               (FK users)
8. siswa_wali         (FK siswa, wali)
9. guru_mapel         (FK guru, mata_pelajaran)
10. jadwal            (FK kelas, mata_pelajaran, guru, tahun_ajaran)
11. komponen_nilai    (FK mata_pelajaran)
12. nilai             (FK siswa, mata_pelajaran, guru, tahun_ajaran)
13. absensi           (FK siswa, kelas, guru)
14. pengumuman        (FK users)
15. pengumuman_target (FK pengumuman, kelas, wali)
16. tagihan_spp       (FK siswa, tahun_ajaran)
17. pembayaran_spp    (FK tagihan_spp, users)
18. notifikasi_whatsapp (FK wali, siswa)
19. chatbot_sessions  (FK wali)
20. chatbot_logs      (FK wali)
21. konfigurasi
```

---

## 5. Logika Nilai Akhir (untuk `NilaiService`)

```
nilai_akhir = (nilai_harian × bobot_harian
             + nilai_uts    × bobot_uts
             + nilai_uas    × bobot_uas) / 100

predikat:  A jika ≥ 85 | B jika ≥ 75 | C jika ≥ 60 | D jika < 60
status_kelulusan: 'tuntas' jika nilai_akhir ≥ kkm, else 'remedial'
```
Validasi input: setiap komponen nilai harus berada pada rentang **0–100**.

---

## 6. Relasi Eloquent (ringkas)

```php
// Siswa
belongsTo(Kelas)  ·  belongsToMany(Wali, 'siswa_wali')  ·  hasMany(Nilai)
hasMany(Absensi)  ·  hasMany(TagihanSpp)

// Wali
belongsTo(User)  ·  belongsToMany(Siswa, 'siswa_wali')  ·  hasMany(ChatbotLog)

// Guru
belongsTo(User)  ·  belongsToMany(MataPelajaran, 'guru_mapel')  ·  hasMany(Jadwal)

// Kelas
belongsTo(TahunAjaran)  ·  belongsTo(Guru, 'wali_kelas_id')  ·  hasMany(Siswa)

// Nilai
belongsTo(Siswa) · belongsTo(MataPelajaran) · belongsTo(Guru) · belongsTo(TahunAjaran)

// TagihanSpp
belongsTo(Siswa) · hasMany(PembayaranSpp)

// PembayaranSpp
belongsTo(TagihanSpp) · belongsTo(User,'diverifikasi_oleh')
```

---

## 7. Index & Performa

Sesuai tahap *Revisi Produk Operasional* (optimalisasi query):
- Index pada FK yang sering difilter: `absensi(tanggal, kelas_id)`, `nilai(tahun_ajaran_id)`,
  `tagihan_spp(status)`, `chatbot_sessions(no_hp)`, `wali(no_wa)`.
- UNIQUE constraint mencegah duplikasi transaksi (nilai & absensi).
