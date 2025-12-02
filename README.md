# 🚀 Laravel CI/CD — GitHub Actions → Jagoan Hosting (SSH Key)

Pipeline ini terdiri dari dua proses utama: **CI (Continuous Integration)** dan **CD (Continuous Deployment)**.  
Semua proses berjalan otomatis setiap kali kamu melakukan **push ke branch `main`**.

---

# 🧩 1. Continuous Integration (CI)

CI bertanggung jawab untuk memastikan bahwa kode yang dikirim aman, tidak rusak, dan siap untuk diproduksi.

### ✔ Yang dilakukan CI:
- **Checkout source code**
- **Setup PHP (8.x)**
- **Copy `.env` untuk environment testing**
- **Install Composer dependencies**
- **Generate aplikasi key**
- **Set permission storage**
- **Setup MariaDB untuk pengujian**
- **Menjalankan migration untuk testing**
- **Menjalankan Unit Test (`php artisan test`)**

### 🎯 Tujuan CI:
Menjamin aplikasi selalu **stabil**, **bebas error**, dan **siap dideploy**.  
Jika CI gagal → deploy **dibatalkan otomatis**.

---

# 🚚 2. Continuous Deployment (CD)

Jika CI lulus, tahap deployment berjalan otomatis ke hosting.

### ✔ Yang dilakukan CD:

#### **A. Build Aplikasi**
- Install Composer (mode production, no-dev)
- Setup Node.js
- Install NPM dependencies
- Build frontend (Vite)
- Generate ZIP build yang bersih:
  - Menghapus `.git`, `.github`, `node_modules`, `tests`, `storage`, `.env`

#### **B. Koneksi ke Server (SSH Key)**
- Menggunakan **private key dari GitHub Secrets**
- Server menggunakan **public key** yang disimpan di panel hosting

#### **C. Upload & Deploy**
- Upload file `deploy.zip` via SCP
- Backup file penting:
  - **Tidak menghapus `.env`**
  - **Tidak menghapus `storage/`**
- Replace file kode dengan build terbaru
- Extract ZIP
- Jalankan optimisasi Laravel:
  - `php artisan optimize:clear`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- Jalankan `php artisan migrate --force`
- Keluarkan dari maintenance mode

### 🎯 Tujuan CD:
Membuat proses deploy menjadi **otomatis**, **aman**, dan **cepat** (tanpa perlu unggah manual ke File Manager).

---

# 🛠 Cara Menggunakan Pipeline CI/CD

1. **Pastikan GitHub Secrets sudah diisi:**
   - `SSH_HOST`
   - `SSH_PORT`
   - `SSH_USERNAME`
   - `SSH_PRIVATE_KEY`

2. **Push kode ke branch `main`:**
   ```bash
   git add .
   git commit -m "Update fitur"
   git push origin main
