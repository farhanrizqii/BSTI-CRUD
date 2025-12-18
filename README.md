# 📦 BSTI CRUD — Laravel Product Management with Object Storage

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://mysql.com)
[![Supabase](https://img.shields.io/badge/Supabase-Storage-green.svg)](https://supabase.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38bdf8.svg)](https://tailwindcss.com)

BSTI CRUD adalah aplikasi **CRUD (Create, Read, Update, Delete)** berbasis **Laravel** yang dilengkapi dengan **upload gambar ke Object Storage (Supabase Storage)**.

Project ini dirancang sebagai contoh implementasi **arsitektur backend modern**, di mana file tidak disimpan di server maupun database, melainkan di layanan object storage eksternal.

---

## ✨ Fitur Utama

- ✅ **CRUD Product** (Create, Read, Update, Delete)
- 🔍 **Search Product** berdasarkan Nama & Deskripsi
- 🖼️ **Upload Gambar** ke Supabase Object Storage
- 🔗 **URL-based Storage** — Database hanya menyimpan URL gambar
- 📄 **Detail Product Page** dengan tampilan modern
- 🎨 **Modern UI** — Responsive design dengan Tailwind CSS
- ⚠️ **Konfirmasi Delete** — Mencegah penghapusan tidak sengaja
- 💬 **Feedback Messages** — Success & error notification
- 🗑️ **Auto Delete** — File di storage ikut terhapus saat delete product

---

## 🧠 Arsitektur Sistem
```
User
 └── Laravel App
      ├── MySQL (data product)
      │    └── Simpan: name, description, image_url
      │
      └── Supabase Storage
           └── Simpan: file gambar (object storage)
```

### 📌 Best Practice Architecture

| ❌ **Jangan** | ✅ **Lakukan** |
|---------------|----------------|
| Simpan file di database (BLOB) | Simpan URL di database |
| Simpan file di `storage/app` | Gunakan Object Storage (S3-like) |
| Upload ke `public/` folder | Upload ke cloud storage |

**Keuntungan:**
- ⚡ **Performa** — Database tidak bloat
- 💰 **Scalable** — Storage terpisah dari server
- 🔒 **Secure** — File management terpusat
- 🌐 **CDN Ready** — Bisa dikombinasikan dengan CDN

---

## 🛠 Tech Stack

| Teknologi | Versi | Keterangan |
|-----------|-------|-----------|
| **Laravel** | 11.x | Backend Framework |
| **PHP** | 8.2+ | Programming Language |
| **MySQL** | 8.0 | Relational Database |
| **Supabase Storage** | Latest | Object Storage (S3-compatible) |
| **Tailwind CSS** | 3.x | UI Framework |
| **Blade** | - | Template Engine |

---

## 📂 Struktur Folder
```
bsti-crud/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ProductController.php
│   └── Models/
│       └── Product.php
│
├── database/
│   └── migrations/
│       └── xxxx_create_products_table.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── products/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── show.blade.php
│
├── routes/
│   └── web.php
│
├── .env.example
└── README.md
```

---

## ⚙️ Instalasi & Setup

### 1️⃣ Clone Repository
```bash
git clone https://github.com/username/bsti-crud.git
cd bsti-crud
```

### 2️⃣ Install Dependencies
```bash
composer install
```

### 3️⃣ Setup Environment

Copy file `.env`:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 4️⃣ Konfigurasi Database

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bsti_crud
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration:
```bash
php artisan migrate
```

### 5️⃣ Konfigurasi Supabase Storage

#### 🔑 Ambil Credential Supabase

1. Buka **Supabase Dashboard**
2. Pilih project Anda
3. Masuk ke **Project Settings → API**
4. Copy:
   - **Project URL**
   - **service_role key** (bukan anon key!)

Tambahkan ke `.env`:
```env
SUPABASE_URL=https://xxxxxxxxxxxx.supabase.co
SUPABASE_KEY=your_service_role_key_here
```

#### 📦 Buat Bucket Storage

1. Masuk ke **Storage** di Supabase Dashboard
2. Klik **New Bucket**
3. Nama bucket: `product-bsti`
4. Set bucket sebagai **Public**

#### 🔐 Setup Policy Supabase (Wajib!)

Jalankan SQL berikut di **Supabase SQL Editor**:
```sql
-- Allow public upload
CREATE POLICY "Allow public upload to product-bsti"
ON storage.objects
FOR INSERT
TO public
WITH CHECK (
  bucket_id = 'product-bsti'
);

-- Allow public read
CREATE POLICY "Allow public read from product-bsti"
ON storage.objects
FOR SELECT
TO public
USING (
  bucket_id = 'product-bsti'
);

-- Allow public delete
CREATE POLICY "Allow public delete from product-bsti"
ON storage.objects
FOR DELETE
TO public
USING (
  bucket_id = 'product-bsti'
);
```

### 6️⃣ Jalankan Aplikasi
```bash
php artisan serve
```

Akses di browser: **http://127.0.0.1:8000**

---

## 🔍 Fitur Search (Nama & Deskripsi)

Search menggunakan **LIKE query** untuk mencocokkan:
- Nama product
- Deskripsi product
```php
Product::where('name', 'like', "%$q%")
       ->orWhere('description', 'like', "%$q%")
       ->get();
```

**Contoh:**
- Search: `"botol"` → Menemukan: "Botol Plastik", "Botol Kaca"
- Search: `"medis"` → Menemukan product dengan deskripsi: "Alat Medis"

---

## 🖼️ Upload Gambar (Object Storage)

### Flow Upload:

1. User upload gambar via form
2. Laravel upload ke **Supabase Storage**
3. Supabase return **public URL**
4. URL disimpan di **MySQL**

### Contoh URL yang Disimpan:
```
https://xxxx.supabase.co/storage/v1/object/public/product-bsti/1734509123_botol.jpg
```

### 📌 File TIDAK disimpan di:
- ❌ `storage/app`
- ❌ `public/uploads`
- ❌ Database (BLOB)

### ✅ File disimpan di:
- ✅ **Supabase Object Storage**
- ✅ Database hanya simpan **URL string**

---

## 🗑️ Delete Product

Saat product dihapus:

1. ✅ File **dihapus dari Supabase Storage**
2. ✅ Data **dihapus dari MySQL**

Ini memastikan tidak ada **orphaned files** (file tanpa data).

---

## 🧪 Contoh Data

| No | Nama | Deskripsi | Gambar |
|----|------|-----------|--------|
| 1 | Kardus | Sampah Organik | Supabase URL |
| 2 | Suntik | Alat Medis | Supabase URL |
| 3 | Botol | Botol Plastik | Supabase URL |

---

## 👤 Author

**Farhan Rizqi Ma'ajid**
