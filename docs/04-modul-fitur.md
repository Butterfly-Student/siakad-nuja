# 04 — Modul & Fitur (Requirement Fungsional)

> Rincian seluruh halaman/antarmuka dari sub-bab *Desain Sistem* (Gambar 3.16–3.38) `KREKSEK_FIXED.docx`.
> Setiap halaman → controller + view + requirement fungsional untuk implementasi Laravel.

---

## A. MODUL AUTENTIKASI

### A1. Halaman Login (Gambar 3.16)
- Form input **username/email** + **password**; opsi "Ingat saya"; tautan "Lupa password".
- Mendukung 3 peran: Administrator, Guru, Orang Tua/Wali → redirect ke tampilan sesuai hak akses.
- Wali dapat login via **nomor HP**.
- Cek `is_active`; verifikasi password (bcrypt).
- **Controller:** `Auth\LoginController` · **Route:** `GET/POST /login`, `POST /logout`.

---

## B. MODUL ADMINISTRATOR

### B1. Dashboard Administrator (Gambar 3.17)
Ringkasan operasional: total siswa, total guru, pengumuman terbaru, rekap absensi hari ini,
tabel ketidakhadiran terbaru, daftar notifikasi & tugas.
- **Controller:** `Admin\DashboardController@index`.

### B2. Manajemen Data Siswa (Gambar 3.18)
- Daftar siswa aktif + kelas, nama wali, no WA wali, status.
- Pencarian, filter (kelas, tahun ajaran), **ekspor Excel**.
- Tambah/edit via **modal** (data pribadi siswa + data wali sekaligus).
- **Controller:** `Admin\SiswaController` (resource) · **Model:** `Siswa`, `Wali`.

### B3. Manajemen Data Guru (Gambar 3.25)
- Daftar guru + NIP, mapel diampu, jumlah kelas, email, status.
- Tambah/edit; **form multi-pilih** mapel & kelas tanggung jawab.
- **Controller:** `Admin\GuruController` · **Pivot:** `guru_mapel`.

### B4. Manajemen Data Orang Tua/Wali (Gambar 3.24)
- Kelola info wali: nama, NIK, hubungan, **no WhatsApp**, alamat.
- **Kaitkan satu akun wali dengan beberapa siswa** (pivot `siswa_wali`).
- **Controller:** `Admin\WaliController`.

### B5. Manajemen Kelas & Mata Pelajaran (Gambar 3.26)
Dua bagian dalam satu tampilan:
- **Kelas:** nama, tingkat, wali kelas, tahun ajaran.
- **Mapel:** kode, nama, tingkat, guru pengampu, jam/minggu.
- **Controller:** `Admin\KelasController`, `Admin\MapelController`.

### B6. Manajemen Pengumuman (Gambar 3.22)
- Buat/kelola pengumuman; kategori; jadwal publikasi; target kelas tertentu.
- Opsi **kirim notifikasi WA otomatis** + pratinjau teks pesan.
- **Controller:** `Admin\PengumumanController` · **Model:** `Pengumuman`, `PengumumanTarget`.

### B7. Konfigurasi Sistem (Gambar 3.27)
- Info umum sekolah; **bobot komponen penilaian default**; **template pesan WA**
  (absensi, nilai, pengumuman); **konfigurasi koneksi gateway WhatsApp**.
- Fitur **pratinjau pesan** & **uji koneksi gateway**.
- **Controller:** `Admin\KonfigurasiController` · **Model:** `Konfigurasi` (key-value).

### B8. Log Notifikasi WhatsApp (Gambar 3.28)
- Riwayat pesan terkirim: waktu, no tujuan, nama wali, nama siswa, jenis, isi, status.
- Filter jenis & status; **kirim ulang** pesan gagal.
- **Controller:** `Admin\LogWaController` · **Model:** `NotifikasiWhatsapp`.

### B9. Manajemen Tagihan & Pembayaran (Gambar 3.35)
- 4 kartu ringkasan: total tagihan bulan berjalan, lunas, menunggu verifikasi, tunggakan.
- Tabel siswa + status pembayaran; **verifikasi bukti transfer**, **tolak dengan alasan**,
  **kirim pengingat WA** individual.
- **Tagihan massal (bulk billing)** untuk semua/kelas tertentu + broadcast WA otomatis.
- Modal verifikasi: detail siswa, info transfer, **lampiran foto bukti**.
- **Controller:** `Admin\SppController` · **Model:** `TagihanSpp`, `PembayaranSpp`.

---

## C. MODUL GURU

### C1. Dashboard Guru (Gambar 3.29)
Ringkasan mengajar: jumlah kelas diampu, total siswa, pengumuman terbaru,
progres absensi hari ini, tabel jadwal, akses cepat absensi & input nilai.
- **Controller:** `Guru\DashboardController`.

### C2. Input Nilai Akademik (Gambar 3.19)
- Input nilai **tugas/Harian, UTS, UAS** per kelas & mapel.
- Hitung **nilai akhir + predikat** otomatis (bobot dapat diubah).
- Setelah simpan → **notifikasi WA nilai** otomatis ke ortu.
- **Controller:** `Guru\NilaiController` · **Service:** `NilaiService`.

### C3. Pencatatan Kehadiran (Gambar 3.20)
- Status: **Hadir, Absen/Alpha, Izin, Sakit** + keterangan.
- Rekap kehadiran kelas langsung di bawah.
- Status **Absen → notifikasi WA otomatis** ke ortu.
- **Controller:** `Guru\AbsensiController`.

### C4. Rekap Kehadiran Kelas (Gambar 3.30)
- Rekap per kelas per bulan: jumlah hadir/absen/izin/sakit, total pertemuan, % kehadiran per siswa.
- **Ekspor PDF/Excel**.
- **Controller:** `Guru\RekapController@kehadiran`.

### C5. Rekap Nilai Kelas (Gambar 3.31)
- Ringkasan nilai kelas per mapel/semester: tugas, UTS, UAS, nilai akhir, predikat, status kelulusan.
- Statistik: rata-rata, tertinggi, terendah, jumlah tuntas/remedial. Ekspor PDF/Excel.
- **Controller:** `Guru\RekapController@nilai`.

### C6. Laporan Akademik (Gambar 3.21)
- Parameter: jenis laporan, kelas, mapel, periode semester.
- Statistik ringkas: rata-rata kelas, % kehadiran, jumlah tuntas, jumlah remedial.
- **Controller:** `Guru\LaporanController` · **Service:** `LaporanService` (PDF/Excel).

---

## D. MODUL ORANG TUA/WALI (Portal Web)

### D1. Dashboard Orang Tua/Wali (Gambar 3.23)
Ringkasan anak: rata-rata nilai, % kehadiran, jumlah absen bulan berjalan, pengumuman baru,
rekap kehadiran, riwayat notifikasi WA terbaru.
- **Controller:** `Wali\DashboardController`.

### D2. Detail Nilai Akademik (Gambar 3.32)
- Rincian nilai anak per mapel/semester: tugas, UTS, UAS, nilai akhir, predikat + catatan guru.
- **Controller:** `Wali\PortalController@nilai`.

### D3. Detail Kehadiran (Gambar 3.33)
- Kehadiran anak per bulan: ringkasan hadir/absen/izin/sakit + % + tabel per tanggal.
- **Controller:** `Wali\PortalController@kehadiran`.

### D4. Pengumuman (Gambar 3.34)
- Daftar pengumuman; filter status baca (semua/belum/sudah); modal isi lengkap.
- **Controller:** `Wali\PortalController@pengumuman`.

### D5. Tagihan & Konfirmasi Pembayaran (Gambar 3.36)
- 3 indikator: belum dibayar, menunggu verifikasi, total lunas tahun ajaran.
- Tab **Tagihan Aktif**: nominal, jatuh tempo, status.
- Tombol **Konfirmasi Pembayaran** → modal unggah bukti transfer (nama tagihan, bank,
  nama pengirim, tanggal, nominal, **file JPG/PNG**).
- Tabel riwayat + **unduh kuitansi PDF** untuk transaksi disetujui.
- **Controller:** `Wali\PembayaranController`.

---

## E. CHATBOT WHATSAPP (Gambar 3.37–3.38)
Detail lengkap di `05-chatbot-whatsapp.md`. Menu utama 5 pilihan:
(1) Info Nilai Rapor, (2) Info Rekap Kehadiran, (3) Info Tagihan & Pembayaran,
(4) Info Agenda/Pengumuman, (5) Hubungi Customer Service.

---

## F. Ringkasan Ekspor & Notifikasi

| Fitur | Format | Pemicu |
|-------|--------|--------|
| Rekap nilai/kehadiran, laporan | PDF, Excel | Manual (guru/admin) |
| Kuitansi pembayaran | PDF | Setelah pembayaran disetujui |
| Ekspor data siswa | Excel | Manual (admin) |
| Notifikasi absen (Alpha) | WA | Otomatis saat absensi disimpan |
| Notifikasi nilai | WA | Otomatis saat nilai disimpan |
| Notifikasi pengumuman | WA | Opsional saat publikasi |
| Notifikasi tagihan/kuitansi | WA | Otomatis saat tagihan terbit / lunas |

> Library PDF disarankan: `barryvdh/laravel-dompdf`. Excel: `maatwebsite/excel`.
