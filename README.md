<h1 align="center">UMKM-Commerce</h1>

UMKM-Commerce adalah aplikasi e-commerce sederhana berbasis Laravel untuk membantu pelaku UMKM menjual produk secara online. Proyek ini mencakup fitur inti: katalog produk, keranjang belanja, checkout, pesanan, ulasan, alamat pengiriman, metode pengiriman, serta autentikasi pengguna (Laravel Breeze).

Database diagram: https://dbdiagram.io/d/68cfea2f960f6d821a15006b

## Fitur
- Katalog & detail produk (+ slug SEO)
- Kategori produk
- Keranjang belanja (tambah, ubah, hapus)
- Checkout dengan alamat pengiriman & metode pengiriman
- Pesanan: daftar & detail
- Alamat pengguna (default address)
- Ulasan produk
- Autentikasi (login/register) via Laravel Breeze (Blade)

## Teknologi
- Laravel 12 (PHP >= 8.2)
- MySQL (Laragon) atau SQLite (opsional)
- Laravel Breeze (Blade), Vite, Tailwind CSS

## Prasyarat
- PHP 8.2+
- Composer
- Node.js 18+
- Laragon (disarankan di Windows) atau lingkungan LAMP/WAMP lain

## Instalasi (Windows + Laragon)

1) Clone repo & masuk folder
```powershell
git clone https://github.com/MohammadIzza/UMKM-Commerce.git
cd UMKM-Commerce
```

2) Pasang dependencies PHP
```powershell
composer install
```

3) Salin env & generate key
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

4) Konfigurasi database
	- Buka file `.env`, set:
		- `DB_CONNECTION=mysql`
		- `DB_HOST=127.0.0.1`
		- `DB_PORT=3306`
		- `DB_DATABASE=umkm_commerce` (buat DB kosong via Laragon/PhpMyAdmin)
		- `DB_USERNAME=root`
		- `DB_PASSWORD=` (kosongkan jika default Laragon)


5) Migrasi & seeder
```powershell
php artisan migrate --seed
```

6) Pasang dependencies frontend & build assets
```powershell
npm install
npm run build
```

7) Menjalankan aplikasi
```powershell
php artisan serve
```
	- Buka http://127.0.0.1:8000

> Untuk pengembangan dengan hot reload, jalankan `npm run dev` pada terminal terpisah.

## Akun Uji (Seeder)
- Admin: `admin@umkm.test` / password: `password`
- Pembeli: `buyer@umkm.test` / password: `password`

## Navigasi Utama
- `/shop` — katalog produk
- `/shop/{slug}` — detail produk (login untuk add-to-cart)
- `/cart` — keranjang (login)
- `/checkout` — checkout (login)
- `/orders` — daftar pesanan (login)
- `/orders/{id}` — detail pesanan (login)
- `/addresses` — alamat pengguna (login)
- `/login`, `/register` — autentikasi Breeze



## Pengujian (opsional)
```powershell
php artisan test
```

## Lisensi
MIT
