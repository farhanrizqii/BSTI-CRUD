# 📦 BSTI CRUD — Laravel Product Management with Object Storage

BSTI CRUD adalah aplikasi **CRUD (Create, Read, Update, Delete)** berbasis **Laravel** yang dilengkapi dengan **upload gambar ke Object Storage (Supabase Storage)**.  
Project ini dirancang sebagai contoh implementasi **arsitektur backend modern**, di mana file tidak disimpan di server maupun database, melainkan di layanan object storage.

---

## ✨ Fitur Utama

- ✅ CRUD Product (Create, Read, Update, Delete)
- 🔍 Search product berdasarkan **Nama & Deskripsi**
- 🖼 Upload gambar ke **Supabase Object Storage**
- 🔗 Database hanya menyimpan **URL gambar**
- 📄 Halaman Detail Product
- 🎨 UI modern & responsif (Tailwind CSS)
- ⚠️ Konfirmasi saat hapus data
- 💬 Feedback success & error message

---

## 🧠 Arsitektur Sistem
User
└── Laravel App
├── MySQL (data product)
│ └── simpan: name, description, image_url
└── Supabase Storage
└── simpan: file gambar (object storage)

📌 **Best Practice:**
- Database ❌ tidak menyimpan file
- Object Storage ✅ menyimpan file
- Database hanya menyimpan **URL file**
