# Skema Database — Sistem Manajemen Sekolah

Skema ini dibuat berdasarkan ERD yang diberikan, terdiri dari 12 tabel: `users`, `guru`, `siswa`, `orang_tua`, `kelas`, `mata_pelajaran`, `jadwal_pelajaran`, `nilai`, `absensi`, `pengumuman`, `chatbot_session`, dan `chatbot_log`.

Dialek: **MySQL 8** (menyesuaikan tipe data `tinyint`, `year`, `json` pada diagram).

> Catatan: panjang `VARCHAR` dan presisi `DECIMAL` tidak tercantum eksplisit di diagram, jadi saya isi dengan nilai yang wajar untuk masing-masing kolom. Sesuaikan bila perlu.

---

## Urutan pembuatan tabel

Urutan di bawah mengikuti dependency foreign key (tabel yang direferensikan dibuat lebih dulu):

1. `users`
2. `guru`
3. `kelas`
4. `mata_pelajaran`
5. `siswa`
6. `jadwal_pelajaran`
7. `pengumuman`
8. `orang_tua`
9. `nilai`
10. `absensi`
11. `chatbot_session`
12. `chatbot_log`

---

## 1. `users`

Akun login untuk semua peran (admin, guru, dsb).

```sql
CREATE TABLE users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(150) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    role        VARCHAR(20)  NOT NULL,
    no_hp       VARCHAR(20)  NULL,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_users_email (email)
);
```

## 2. `guru`

Data guru, terhubung 1-1 ke `users`.

```sql
CREATE TABLE guru (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    nip           VARCHAR(30)  NOT NULL,
    nama_lengkap  VARCHAR(150) NOT NULL,
    jabatan       VARCHAR(50)  NULL,
    no_hp         VARCHAR(20)  NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_guru_nip (nip),
    UNIQUE KEY uq_guru_user_id (user_id),
    CONSTRAINT fk_guru_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
```

## 3. `kelas`

Data kelas/rombel. `wali_kelas_id` merujuk ke `guru`.

```sql
CREATE TABLE kelas (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kelas     VARCHAR(50)  NOT NULL,
    tingkat        VARCHAR(10)  NOT NULL,
    jenjang        VARCHAR(20)  NOT NULL,
    tahun_ajaran   VARCHAR(15)  NOT NULL,
    wali_kelas_id  INT UNSIGNED NULL,
    kapasitas      TINYINT UNSIGNED NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_kelas_wali_guru
        FOREIGN KEY (wali_kelas_id) REFERENCES guru (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);
```

## 4. `mata_pelajaran`

Master data mata pelajaran.

```sql
CREATE TABLE mata_pelajaran (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_mapel  VARCHAR(20)  NOT NULL,
    nama_mapel  VARCHAR(100) NOT NULL,
    jenjang     VARCHAR(20)  NOT NULL,
    kkm         TINYINT UNSIGNED NULL,
    deskripsi   TEXT         NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_mapel_kode (kode_mapel)
);
```

## 5. `siswa`

Data siswa, terhubung ke `kelas`.

```sql
CREATE TABLE siswa (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nis            VARCHAR(30)  NOT NULL,
    nama_lengkap   VARCHAR(150) NOT NULL,
    kelas_id       INT UNSIGNED NOT NULL,
    tanggal_lahir  DATE         NULL,
    jenis_kelamin  VARCHAR(10)  NULL,
    alamat         TEXT         NULL,
    foto           VARCHAR(255) NULL,
    status         VARCHAR(20)  NULL,
    tahun_masuk    YEAR         NOT NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_siswa_nis (nis),
    CONSTRAINT fk_siswa_kelas
        FOREIGN KEY (kelas_id) REFERENCES kelas (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
```

## 6. `jadwal_pelajaran`

Jadwal per kelas, mapel, dan guru pengajar.

```sql
CREATE TABLE jadwal_pelajaran (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kelas_id      INT UNSIGNED NOT NULL,
    mapel_id      INT UNSIGNED NOT NULL,
    guru_id       INT UNSIGNED NOT NULL,
    hari          VARCHAR(15)  NOT NULL,
    jam_ke        TINYINT UNSIGNED NOT NULL,
    jam_mulai     TIME         NOT NULL,
    jam_selesai   TIME         NOT NULL,
    ruangan       VARCHAR(50)  NULL,
    tahun_ajaran  VARCHAR(15)  NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_jadwal_kelas
        FOREIGN KEY (kelas_id) REFERENCES kelas (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_jadwal_mapel
        FOREIGN KEY (mapel_id) REFERENCES mata_pelajaran (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_jadwal_guru
        FOREIGN KEY (guru_id) REFERENCES guru (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
```

## 7. `pengumuman`

Pengumuman yang dibuat oleh user (admin/guru).

```sql
CREATE TABLE pengumuman (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul            VARCHAR(200) NOT NULL,
    konten           TEXT         NOT NULL,
    target_role      VARCHAR(20)  NULL,
    dibuat_oleh      INT UNSIGNED NOT NULL,
    tanggal_publish  DATE         NULL,
    is_active        BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pengumuman_user
        FOREIGN KEY (dibuat_oleh) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
```

## 8. `orang_tua`

Data orang tua/wali, terhubung ke `siswa`.

```sql
CREATE TABLE orang_tua (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id          INT UNSIGNED NOT NULL,
    nama              VARCHAR(150) NOT NULL,
    hubungan          VARCHAR(30)  NULL,
    no_hp             VARCHAR(20)  NULL,
    alamat            TEXT         NULL,
    pekerjaan         VARCHAR(100) NULL,
    is_kontak_utama   BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_ortu_siswa
        FOREIGN KEY (siswa_id) REFERENCES siswa (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
```

## 9. `nilai`

Nilai siswa per mapel, per kelas, per semester.

```sql
CREATE TABLE nilai (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id      INT UNSIGNED NOT NULL,
    mapel_id      INT UNSIGNED NOT NULL,
    kelas_id      INT UNSIGNED NOT NULL,
    semester      VARCHAR(10)  NOT NULL,
    tahun_ajaran  VARCHAR(15)  NOT NULL,
    nilai_harian  DECIMAL(5,2) NULL,
    nilai_uts     DECIMAL(5,2) NULL,
    nilai_uas     DECIMAL(5,2) NULL,
    nilai_akhir   DECIMAL(5,2) NULL,
    predikat      VARCHAR(5)   NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_nilai_siswa
        FOREIGN KEY (siswa_id) REFERENCES siswa (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_nilai_mapel
        FOREIGN KEY (mapel_id) REFERENCES mata_pelajaran (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_nilai_kelas
        FOREIGN KEY (kelas_id) REFERENCES kelas (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
```

## 10. `absensi`

Presensi siswa per jadwal pelajaran.

```sql
CREATE TABLE absensi (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    siswa_id     INT UNSIGNED NOT NULL,
    jadwal_id    INT UNSIGNED NOT NULL,
    tanggal      DATE         NOT NULL,
    status       VARCHAR(20)  NULL,
    keterangan   TEXT         NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_absensi_siswa
        FOREIGN KEY (siswa_id) REFERENCES siswa (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_absensi_jadwal
        FOREIGN KEY (jadwal_id) REFERENCES jadwal_pelajaran (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
```

## 11. `chatbot_session`

Sesi percakapan chatbot berbasis nomor HP.

```sql
CREATE TABLE chatbot_session (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_hp          VARCHAR(20)  NOT NULL,
    state          VARCHAR(50)  NULL,
    data_context   JSON         NULL,
    last_activity  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_chatbot_session_no_hp (no_hp)
);
```

## 12. `chatbot_log`

Log riwayat pesan chatbot, opsional terhubung ke `siswa`.

```sql
CREATE TABLE chatbot_log (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_hp         VARCHAR(20)  NOT NULL,
    pesan_masuk   TEXT         NULL,
    pesan_keluar  TEXT         NULL,
    siswa_id      INT UNSIGNED NULL,
    intent        VARCHAR(50)  NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_chatbotlog_siswa
        FOREIGN KEY (siswa_id) REFERENCES siswa (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);
```

---

## Ringkasan relasi

| Tabel Anak | Kolom FK | Tabel Induk | Kolom Induk |
|---|---|---|---|
| guru | user_id | users | id |
| kelas | wali_kelas_id | guru | id |
| siswa | kelas_id | kelas | id |
| jadwal_pelajaran | kelas_id | kelas | id |
| jadwal_pelajaran | mapel_id | mata_pelajaran | id |
| jadwal_pelajaran | guru_id | guru | id |
| pengumuman | dibuat_oleh | users | id |
| orang_tua | siswa_id | siswa | id |
| nilai | siswa_id | siswa | id |
| nilai | mapel_id | mata_pelajaran | id |
| nilai | kelas_id | kelas | id |
| absensi | siswa_id | siswa | id |
| absensi | jadwal_id | jadwal_pelajaran | id |
| chatbot_log | siswa_id | siswa | id |

---

## Script gabungan (satu file)

Jika ingin menjalankan semuanya sekaligus, gabungkan blok-blok `CREATE TABLE` di atas sesuai urutan pada bagian **"Urutan pembuatan tabel"**, karena MySQL akan menolak foreign key yang menunjuk ke tabel yang belum ada.
