# 05 — Spesifikasi Chatbot WhatsApp

> Sumber: BAB II §2.2.4 & §2.2.5, flowchart Gambar 3.5, tampilan chatbot Gambar 3.37–3.38.
> Chatbot **rule-based** dengan pendekatan **Finite State Machine (FSM)**, terhubung langsung ke DB SIAKAD.

---

## 1. Karakteristik

- **Tipe:** rule-based (bukan AI/NLP) → pencocokan pola/keyword & pohon keputusan.
- **Pola interaksi:** menu berbasis angka (orang tua mengetik angka pilihan).
- **State management:** FSM dengan **timeout sesi 30 menit** (tabel `chatbot_sessions`).
- **Akun:** akun resmi terverifikasi "SIAKAD Nurul Jadid Karduluk".
- **Dua mode:**
  1. **Notifikasi otomatis** (outbound, dipicu event sistem).
  2. **Respons interaktif** (inbound, orang tua bertanya).

Arsitektur rule-based (BAB II §2.2.4): (a) modul penerima pesan, (b) mesin pencocokan pola
(keyword/regex), (c) basis pengetahuan terhubung DB, (d) modul pengirim respons.

---

## 2. Menu Utama (Gambar 3.37)

```
🏫 SIAKAD Nurul Jadid Karduluk
Selamat datang, {nama_wali}.

Ketik angka layanan:
[1] Info Nilai Rapor Siswa
[2] Info Rekap Kehadiran
[3] Info Tagihan & Pembayaran
[4] Info Agenda Sekolah Terbaru
[5] Hubungi Customer Service

Ketik 'MENU' kapan saja untuk kembali.
```

---

## 3. State Machine (FSM)

```
                 ┌────────────────┐
   (pesan masuk) │  START/AUTH    │  validasi no_hp → wali
                 └───────┬────────┘
                         ▼
                 ┌────────────────┐  ketik 'MENU' dari state manapun
        ┌───────▶│  MENU_UTAMA    │◀──────────────────────────┐
        │        └───────┬────────┘                           │
        │      1 │ 2 │ 3 │ 4 │ 5                               │
        │        ▼   ▼   ▼   ▼   ▼                              │
        │  ┌─────────┬───────────┬──────────┬────────────┬──────────────┐
        │  │MENU_    │MENU_      │MENU_SPP  │MENU_AGENDA │CS (broadcast │
        │  │NILAI    │KEHADIRAN  │          │            │ nomor admin) │
        │  └────┬────┴─────┬─────┴────┬─────┴─────┬──────┴──────────────┘
        │       ▼          ▼          ▼           ▼
        │   query D2    query D2   query D1     query D1
        │   (nilai)     (absensi)  (spp)        (pengumuman)
        │       │          │          │           │
        └───────┴──────────┴──────────┴───────────┘  (balas + kembali MENU_UTAMA)

  Timeout 30 mnt / sesi baru → reset ke MENU_UTAMA.
  Input tidak dikenal → pesan error informatif + tampilkan menu.
```

| State | Pemicu | Aksi | Sumber data |
|-------|--------|------|-------------|
| `MENU_UTAMA` | default / 'MENU' | Tampilkan 5 pilihan | — |
| `MENU_NILAI` | ketik `1` | Rata-rata + daftar mapel (skor, Tuntas/Remedial) | `nilai` (D2) |
| `MENU_KEHADIRAN` | ketik `2` | Rekap hadir/izin/sakit/alpha + % | `absensi` (D2) |
| `MENU_SPP` | ketik `3` | Status tagihan, tunggakan, nominal | `tagihan_spp` (D1) |
| `MENU_AGENDA` | ketik `4` | Pengumuman/agenda terbaru | `pengumuman` (D1) |
| `CS` | ketik `5` | Info kontak customer service | `konfigurasi` |

---

## 4. Alur Pemrosesan Pesan Masuk (Gambar 3.5)

```
1. Pesan WA masuk → Gateway kirim webhook POST → POST /api/webhook/whatsapp
2. Parse JSON → ekstrak { no_hp, isi_pesan }
3. Cek no_hp di tabel wali (wali.no_wa):
   - TIDAK terdaftar → balas "Nomor tidak terdaftar" + catat chatbot_log → SELESAI
   - Terdaftar → ambil data siswa terkait (siswa_wali)
4. Cek chatbot_sessions untuk no_hp:
   - last_activity > 30 mnt / tidak ada → reset state = MENU_UTAMA
   - masih aktif → gunakan state tersimpan
5. Routing berdasarkan state + isi pesan (rule/keyword matching)
6. Query DB sesuai intent → susun balasan
7. Simpan chatbot_log (arah=masuk & keluar, intent)
8. Kirim balasan ke Gateway (HTTP POST) → update sesi (state, last_activity)
```

---

## 5. Kontrak Webhook (Inbound)

**Endpoint:** `POST /api/webhook/whatsapp` (tanpa `auth`, diproteksi verifikasi token gateway).

Contoh payload (bervariasi per provider — normalisasi di controller):
```json
{ "sender": "6281234567890", "message": "1", "timestamp": 1720900000 }
```

**Controller:** `Api\WhatsappWebhookController@handle` →
`ChatbotService::process($noHp, $pesan)` → mengembalikan string balasan →
`WhatsappGatewayService::send($noHp, $balasan)`.

---

## 6. Pengiriman Pesan (Outbound)

**Service:** `WhatsappGatewayService::send(string $noHp, string $pesan): bool`
- Kirim `HTTP POST` ke endpoint gateway (URL & token dari tabel `konfigurasi`).
- Dijalankan lewat **Queue Job** `SendWhatsappMessage` (retry bila gagal).
- Catat ke `notifikasi_whatsapp` (untuk notifikasi sistem) atau `chatbot_logs` (untuk balasan chatbot).

Contoh (format umum, mis. Fonnte):
```php
Http::withToken($token)->post($gatewayUrl.'/send', [
    'target'  => $noHp,
    'message' => $pesan,
]);
```

---

## 7. Notifikasi Otomatis (Event → WA)

| Event | Pemicu | Template (konfigurasi) |
|-------|--------|------------------------|
| Absensi Alpha/Absen | `AbsensiObserver` saat status disimpan | `template_absensi` |
| Nilai baru | setelah `NilaiController@store` | `template_nilai` |
| Pengumuman | opsional saat publikasi (`kirim_wa`) | `template_pengumuman` |
| Tagihan terbit | saat bulk billing / tagihan dibuat | `template_tagihan` |
| Pembayaran lunas | setelah verifikasi disetujui | `template_kuitansi` |

Implementasi via **Laravel Notification** (channel custom `whatsapp`) atau dispatch Job langsung.

Template mendukung placeholder, mis:
```
Yth. Bpk/Ibu {nama_wali},
Ananda {nama_siswa} tercatat *{status}* pada {tanggal}.
- SIAKAD Nurul Jadid Karduluk
```

---

## 8. Penanganan Kesalahan (Gambar 3.38)

- Input tidak dikenal → balasan error informatif + arahkan kembali ke pilihan valid,
  tanpa mengganggu alur percakapan.
- Nomor tidak terdaftar → tolak dengan pesan jelas + catat log.
- Gateway gagal kirim → job retry + status `gagal` di log; admin dapat kirim ulang (Halaman B8).

---

## 9. Contoh Sesi Interaktif (Gambar 3.38)

```
Ortu : 1
Bot  : 📊 Nilai Ananda {nama} — Semester {x}
       Rata-rata: 84.5
       • Matematika: 88 (Tuntas)
       • B. Indonesia: 79 (Tuntas)
       • IPA: 72 (Remedial)
       Ketik 'MENU' untuk kembali.
Ortu : xyz
Bot  : Maaf, perintah tidak dikenali. Ketik 'MENU' untuk melihat pilihan layanan.
```

---

## 10. Catatan Implementasi (rekomendasi)

- Gunakan **Baileys** (Node.js sidecar) atau layanan pihak ketiga (**Fonnte/Wablas**) —
  keduanya menyediakan REST API + webhook. Pilihan disimpan di `konfigurasi`.
- `ChatbotService` sebaiknya berupa kelas FSM murni (mudah di-*unit test* tanpa hit gateway).
- Simpan `context` sesi sebagai JSON untuk alur bertingkat (mis. pilih semester dulu).
- Rule matching: mulai dari exact-match angka menu; siapkan sinonim keyword
  (mis. "nilai", "rapor" → intent nilai) untuk pengembangan lanjutan.
