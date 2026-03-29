# Rutan Kelas IIA Batista Website

Website resmi **Rutan Kelas IIA Batista** (Rumah Tahanan Negara Kelas IIA Batista) yang dikelola oleh Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia.

## 📋 Deskripsi

Website ini berfungsi sebagai portal informasi publik yang menyajikan:

- **Profil Institusi** - Sejarah, visi, misi, struktur organisasi, dan profil pejabat
- **Berita Terkini** - Informasi kegiatan dan pengumuman dari Rutan Kelas IIA Batista
- **Layanan Publik** - Informasi layanan kunjungan, survey kepuasan masyarakat (SKM)
- **Zona Integritas** - Program pencegahan korupsi dan peningkatan layanan publik

## 🛠️ Teknologi

- **Backend**: PHP Native (Vanilla PHP)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Server**: Apache (XAMPP)

### Fitur Teknis

| Fitur | Implementasi |
|--------|--------------|
| Keamanan | CSRF Token, XSS Prevention, Rate Limiting, CSP Headers |
| SEO | Meta Tags, Open Graph, Twitter Card, sitemap.xml, robots.txt |
| Performa | Browser Caching, Gzip Compression, Lazy Loading Images, WebP |
| URL | Clean URLs (mod_rewrite) |

## 📁 Struktur Direktori

```
web-rutan/
├── admin/                  # Panel admin (CRUD data)
│   ├── includes/          # Sidebar & komponen admin
│   ├── js/                # JavaScript admin
│   └── css/               # Styling admin
├── backend/
│   ├── api/               # REST API endpoints
│   ├── config/             # Konfigurasi database & aplikasi
│   ├── includes/           # Helper functions, header/footer partials
│   └── uploads/            # File uploads (gambar berita, slider, dll)
├── pages/                  # Halaman publik
├── css/                    # Stylesheet
├── js/                     # JavaScript
├── images/                 # Gambar statis
├── pdfs/                   # Dokumen PDF publik
├── index.php               # Halaman beranda
├── header.php              # Partial header dengan navbar
└── .htaccess               # Konfigurasi Apache
```

## 🚀 Instalasi

### Prerequisites

- PHP >= 7.4
- MySQL/MariaDB
- Apache dengan mod_rewrite enabled

### Steps

1. **Clone repository**
   ```bash
   git clone https://github.com/ripaldomarbun/rutan-kelas-iia-batam.git
   ```

2. **Setup database**
   ```bash
   mysql -u root -p < backend/database.sql
   ```

3. **Konfigurasi**
   
   Buat file `backend/config/config.php`:
   ```php
   <?php
   define('BASE_URL', 'http://localhost/web-rutan');
   
   // Database configuration (jika belum di database.php)
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'rutan_db');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   ```

4. **Akses website**
   
   Buka browser: `http://localhost/web-rutan`

## 🔧 Panel Admin

Akses panel admin di: `http://localhost/web-rutan/admin/login.php`

### Fitur Admin

- CRUD Berita
- CRUD Slider
- CRUD Pejabat
- Kelola Layanan Kunjungan
- Survey Kepuasan Masyarakat (SKM)
- Kelola Komitmen & Maklumat

## 📝 API Endpoints

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/backend/api/berita.php` | GET | List berita |
| `/backend/api/search.php?q=keyword` | GET | Pencarian berita |
| `/backend/api/slider.php` | GET | List slider aktif |
| `/backend/api/kunjungan.php` | POST | Submit kunjungan online |
| `/backend/api/survey.php` | POST | Submit survey kepuasan |

## 🔒 Keamanan

- **CSRF Protection**: Semua form menggunakan token CSRF
- **XSS Prevention**: Input sanitization menggunakan `htmlspecialchars` dan `strip_tags`
- **Rate Limiting**: Pembatasan percobaan login (5x per 15 menit)
- **CSP Headers**: Content Security Policy untuk mencegah XSS
- **SQL Injection**: Prepared statements menggunakan PDO

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## 📄 Lisensi

Hak Cipta © <?= date('Y') ?> Rutan Kelas IIA Batista. Hak Cipta Dilindungi.

## 👤 Kontak

**Rutan Kelas IIA Batista**
- Alamat: [Alamat Rutan]
- Telepon: [Nomor Telepon]
- Email: [Email Resmi]

---

Dikembangkan dengan ❤️ oleh Tim IT Rutan Kelas IIA Batista
