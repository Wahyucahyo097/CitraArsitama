# Admin Panel - Citra Arsitama

Panel admin lengkap dengan MySQL, CRUD operations, sidebar, dan design yang modern.

## Fitur

✅ Login system dengan autentikasi MySQL
✅ Dashboard dengan statistik
✅ CRUD untuk Portfolio, Services, Team, Testimonials, Clients
✅ Sidebar fixed di sebelah kiri
✅ Design responsive dan modern dengan Bootstrap 5
✅ Upload file gambar
✅ Settings untuk mengubah profil dan password

## Setup Instructions

### 1. Database Setup

1. Pastikan MySQL server sudah berjalan di Laragon
2. Buka browser dan akses: `http://localhost/Dewi-1.0.0/admin/setup.php`
3. Script akan membuat database `citra_arsitama` dan semua tabel yang dibutuhkan
4. Default admin account:
   - Email: `admin@citraarsitama.com`
   - Password: `admin123`

### 2. Login ke Admin Panel

1. Buka: `http://localhost/Dewi-1.0.0/admin/login.php`
2. Masukkan email dan password default
3. Setelah login, Anda akan diarahkan ke dashboard

### 3. Mengelola Konten

**Portfolio**
- Tambah, edit, hapus portfolio items
- Upload gambar dengan format JPEG/PNG
- Kategorikan ke: App, Product, Branding, Books

**Services**
- Tambah layanan dengan icon Bootstrap Icons
- Deskripsi lengkap untuk setiap service

**Team**
- Kelola data anggota tim
- Upload foto profil
- Simpan social media links (JSON format)

**Testimonials**
- Tambah review dari klien
- Rating sistem (1-5 bintang)
- Upload foto klien

**Clients**
- Galeri logo klien
- Tampilan grid yang responsive

**Settings**
- Update profil admin
- Change password

## File Structure

```
admin/
├── setup.php              # Setup database (jalankan sekali)
├── login.php              # Halaman login
├── index.php              # Dashboard
├── portfolio.php          # CRUD Portfolio
├── services.php           # CRUD Services
├── team.php               # CRUD Team
├── testimonials.php       # CRUD Testimonials
├── clients.php            # CRUD Clients
├── settings.php           # Settings admin
├── logout.php             # Logout script
├── config.php             # Konfigurasi database
├── css/
│   └── admin.css          # Styling admin panel
└── data/
    └── users.sqlite       # (Legacy, tidak digunakan)
```

## Configuration

Edit file `admin/config.php` untuk mengubah database credentials:

```php
define('DB_HOST', 'localhost');    // Host MySQL
define('DB_USER', 'root');         // Username MySQL
define('DB_PASS', '');             // Password MySQL
define('DB_NAME', 'citra_arsitama'); // Database name
```

## Security Tips

1. **Hapus setup.php** setelah selesai setup untuk keamanan
2. **Ubah password default** segera setelah login pertama kali
3. **Gunakan HTTPS** pada server production
4. **Proteksi folder admin** dengan .htaccess atau password
5. **Backup database** secara regular

## Integrasi dengan Website

Untuk menampilkan data dari admin panel ke website:

### Portfolio

```php
<?php
include 'admin/config.php';
$portfolio = $conn->query("SELECT * FROM portfolio WHERE category = 'App' LIMIT 6");
while($row = $portfolio->fetch_assoc()) {
    echo '<div class="portfolio-item">';
    echo '<img src="assets/img/portfolio/' . $row['image'] . '">';
    echo '<h3>' . $row['title'] . '</h3>';
    echo '<p>' . $row['description'] . '</p>';
    echo '</div>';
}
?>
```

### Services

```php
<?php
include 'admin/config.php';
$services = $conn->query("SELECT * FROM services");
while($row = $services->fetch_assoc()) {
    echo '<div class="service">';
    echo '<i class="bi ' . $row['icon'] . '"></i>';
    echo '<h4>' . $row['title'] . '</h4>';
    echo '<p>' . $row['description'] . '</p>';
    echo '</div>';
}
?>
```

## Troubleshooting

**Error: "Connection failed"**
- Pastikan MySQL server aktif di Laragon
- Check database credentials di config.php

**Error: "Table doesn't exist"**
- Jalankan setup.php lagi untuk create tables
- Pastikan database sudah dipilih

**Gambar tidak upload**
- Create folder: `assets/img/portfolio/`, `assets/img/team/`, `assets/img/testimonials/`, `assets/img/clients/`
- Pastikan folder memiliki permission write (chmod 755)

## Browser Support

- Chrome/Chromium
- Firefox
- Safari
- Edge
- Modern mobile browsers

## Support & Updates

Untuk bantuan atau update, silakan hubungi tim development.

---

**Dibuat oleh:** Development Team
**Versi:** 1.0.0
**Last Updated:** Desember 2024
