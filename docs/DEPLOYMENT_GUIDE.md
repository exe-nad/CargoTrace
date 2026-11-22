# CargoTrace &mdash; Panduan Hosting Internasional (Global Cloud Deployment)

Dokumen ini menjelaskan langkah-langkah mempublikasikan platform **CargoTrace** ke internet secara internasional agar dapat diakses dari seluruh dunia oleh dosen, penguji, maupun publik melalui koneksi aman HTTPS.

---

## Opsi 1: Render.com (Direkomendasikan & 100% Gratis)

**Render.com** adalah platform cloud hosting internasional yang otomatis terhubung ke repositori GitHub Anda. Setiap kali Anda melakukan `git push`, Render akan otomatis membangun (*build*) dan memperbarui website secara online.

### Langkah-langkah:
1. Buka [https://render.com](https://render.com) dan klik **Get Started** / **Sign In** menggunakan akun **GitHub** Anda.
2. Di halaman Dashboard Render, klik tombol **New +** di pojok kanan atas, lalu pilih **Web Service**.
3. Pilih opsi **Build and deploy from a Git repository**.
4. Cari dan pilih repositori Anda: `cargotrace` (atau `cargotrace-uas`), lalu klik **Connect**.
5. Isi konfigurasi sederhana:
   - **Name**: `cargotrace` (atau nama unik yang Anda inginkan, misal: `cargotrace-global`)
   - **Region**: `Singapore` (tercepat untuk akses Indonesia & Internasional) atau `Frankfurt / Oregon`
   - **Environment**: `Docker` (Render akan otomatis mendeteksi file `Dockerfile` dan `render.yaml` yang sudah kami siapkan)
   - **Instance Type**: Pilih **Free** ($0 / bulan)
6. Di bagian **Environment Variables** (opsional), tambahkan jika diperlukan:
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
7. Klik **Create Web Service**.
8. Tunggu proses build selesai sekitar 2-3 menit.
9. Website Anda langsung online dengan domain internasional gratis:
   ```text
   https://cargotrace-xxxx.onrender.com
   ```
   *Link ini siap diserahkan ke dosen dan bisa diakses dari perangkat apa pun di seluruh dunia!*

---

## Opsi 2: Railway.app (Alternatif Cepat & Otomatis)

1. Buka [https://railway.app](https://railway.app) dan login dengan akun **GitHub**.
2. Klik **New Project** &rarr; **Deploy from GitHub repo**.
3. Pilih repositori `cargotrace`.
4. Railway akan otomatis mendeteksi Dockerfile dan melakukan deployment.
5. Setelah selesai, buka tab **Settings** &rarr; di bagian **Networking**, klik **Generate Domain**.
6. Anda akan mendapatkan URL publik internasional, misalnya:
   ```text
   https://cargotrace.up.railway.app
   ```

---

## Opsi 3: Akses Publik Internasional Instan dalam 1 Menit (Cloudflare Tunnel)

Jika dosen ingin memeriksa website Anda **sekarang juga** langsung dari laptop tanpa menunggu build cloud:

1. Buka PowerShell dan unduh Cloudflared (gratis resmi dari Cloudflare):
   ```powershell
   winget install --id Cloudflare.cloudflared
   ```
2. Jalankan aplikasi Laravel di laptop Anda:
   ```powershell
   php artisan serve --port=8000
   ```
3. Di jendela PowerShell baru, jalankan terowongan publik internasional:
   ```powershell
   cloudflared tunnel --url http://127.0.0.1:8000
   ```
4. Cloudflare akan langsung memberikan link publik HTTPS internasional acak gratis, contoh:
   ```text
   https://random-words.trycloudflare.com
   ```
   *Link tersebut dapat diakses oleh siapa saja di seluruh dunia secara langsung selama server lokal Anda menyala.*

---

## Kredensial Akun Evaluator untuk Dosen di Web Publik

| Role / Hak Akses | Email Login | Password Default | Catatan |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | `password123` | Akses penuh (Dashboard, CRUD Shipment, Audit Log, System Settings) |
| **Logistics Operator** | `operator@cargotrace.com` | `password123` | Manajemen pergerakan armada, log checkpoint, resolusi insiden exception |
| **Consignee / Customer** | `customer@cargotrace.com` | `password123` | Pelacakan kargo, unduh waybill resmi, monitoring notifikasi kedatangan |
