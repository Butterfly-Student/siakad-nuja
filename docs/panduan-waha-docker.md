# Panduan: Menjalankan WAHA (WhatsApp HTTP API) dengan Docker

> **WAHA** adalah proyek open-source yang membungkus WhatsApp Web menjadi REST API yang dapat kita panggil dari aplikasi Laravel.

---

## Prasyarat

- **Docker Desktop** sudah terinstal dan berjalan
  - Download: https://www.docker.com/products/docker-desktop/
- Koneksi internet aktif

---

## 1. Menjalankan WAHA (Core — Gratis)

Buka terminal dan jalankan:

```bash
docker run -d \
  --name waha \
  --restart unless-stopped \
  -p 3000:3000 \
  devlikeapro/waha
```

Untuk Windows (PowerShell / CMD):

```powershell
docker run -d --name waha --restart unless-stopped -p 3000:3000 devlikeapro/waha
```

### Verifikasi

Buka browser dan akses: **http://localhost:3000/dashboard**

Anda akan melihat dashboard WAHA. Jika muncul halaman dashboard, WAHA sudah berjalan.

---

## 2. Menghubungkan WhatsApp (Scan QR)

### Via Dashboard WAHA

1. Buka `http://localhost:3000/dashboard`
2. Klik **"Start Session"** (nama sesi: `default`)
3. Klik **"Get QR"**
4. Buka WhatsApp di HP → Tap ikon ⋮ → **Linked Devices** → **Link a Device**
5. Scan QR yang tampil di browser

### Via Admin Panel SIAKAD

1. Login ke SIAKAD sebagai Admin
2. Klik menu **WhatsApp Gateway** di sidebar
3. Halaman akan menampilkan QR Code secara otomatis jika belum terhubung
4. Scan QR menggunakan HP

---

## 3. Konfigurasi Webhook (Penting!)

Setelah WhatsApp terhubung, WAHA perlu tahu kemana mengirimkan pesan masuk.

### Via API

```bash
# Daftarkan webhook ke WAHA
curl -X PUT http://localhost:3000/api/sessions/default/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://host.docker.internal:8000/api/webhook/whatsapp",
    "events": ["message"]
  }'
```

> **`host.docker.internal`** adalah hostname khusus yang memungkinkan container Docker mengakses aplikasi yang berjalan di mesin host (Windows/Mac). Di Linux, gunakan IP host Anda.

### Via Dashboard WAHA

1. Di dashboard WAHA, pilih sesi `default`
2. Klik **Settings** atau **Webhooks**
3. Masukkan URL: `http://host.docker.internal:8000/api/webhook/whatsapp`
4. Aktifkan event **`message`**
5. Simpan

---

## 4. Konfigurasi .env Laravel

Tambahkan ke file `.env` Anda:

```env
WAHA_URL=http://localhost:3000
WAHA_SESSION=default
WAHA_API_KEY=
```

---

## 5. Menguji Integrasi

### Test Kirim Pesan

```powershell
# Test kirim pesan (dari terminal)
Invoke-RestMethod -Method POST `
  -Uri "http://localhost:3000/api/sendText" `
  -ContentType "application/json" `
  -Body '{"session":"default","chatId":"628xxx@c.us","text":"Test dari SIAKAD!"}'
```

### Test Webhook (Simulasi Pesan Masuk)

```bash
# Simulasi pesan masuk ke webhook Laravel
curl.exe -X POST http://localhost:8000/api/webhook/whatsapp `
  -H "Content-Type: application/json" `
  -d '{"event":"message","session":"default","payload":{"from":"6281234567890@c.us","body":"MENU","fromMe":false}}'
```

---

## 6. Pengelolaan Container WAHA

```bash
# Lihat status
docker ps -a | findstr waha

# Stop WAHA
docker stop waha

# Start lagi
docker start waha

# Lihat log WAHA
docker logs waha --tail 50

# Restart
docker restart waha

# Hapus (jika ingin reset total)
docker rm -f waha
```

---

## 7. Untuk Server Produksi

Di server produksi, ubah URL webhook dari `host.docker.internal` ke domain/IP server Laravel:

```bash
# Contoh jika Laravel ada di domain siakad.madrasah.sch.id
docker run -d \
  --name waha \
  --restart unless-stopped \
  -p 3000:3000 \
  -e WHATSAPP_HOOK_EVENTS=message \
  -e WHATSAPP_HOOK_URL=https://siakad.madrasah.sch.id/api/webhook/whatsapp \
  devlikeapro/waha
```

> **Catatan keamanan:** Di produksi, pastikan port 3000 WAHA tidak diakses publik. Lindungi dengan firewall dan hanya izinkan akses dari server Laravel sendiri.

---

## 8. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| QR tidak muncul | Cek `docker logs waha` — mungkin container belum siap |
| Webhook tidak diterima | Pastikan URL menggunakan `host.docker.internal` (bukan `localhost`) |
| Pesan tidak terkirim | Cek status koneksi di `/whatsapp` Admin Panel |
| Container crash | Jalankan `docker restart waha` |
| Session expired | Scan ulang QR dari Admin Panel |
