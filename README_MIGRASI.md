# SI BanTal - Migrasi PHP Native ke Laravel

Project ini adalah hasil migrasi awal dari `PemwebKelompok4ParD` (PHP native) ke struktur Laravel pada folder `sibantal-laravel`.

## Yang sudah dipindahkan

- Routing Laravel untuk halaman lama:
  - `/index.php`
  - `/login.php`
  - `/register.php`
  - `/logout.php`
  - `/contact.php`
  - `/portofolio.php`
  - `/detail.php`
  - `/dashboard-admin.php`
  - `/dashboard-desa.php`
  - `/dashboard-donatur.php`
  - `/edit.php`
  - `/edit_program.php`
  - halaman biodata dan about
- Controller utama:
  - `app/Http/Controllers/LegacyController.php`
- View Blade:
  - `resources/views/legacy/*.blade.php`
  - `resources/views/layouts/header.blade.php`
  - `resources/views/layouts/footer.blade.php`
- Migration database:
  - `users`
  - `permintaan_bantuan`
  - `penawaran_bantuan`
  - `history_penyaluran`
- Seeder akun admin:
  - Email: `admin@sibantal.com`
  - Password: `admin123`
- Asset lama sudah dipindahkan ke `public/`, termasuk:
  - gambar
  - `dist/output.css`
  - folder `uploads`
- File SQL asli disimpan di:
  - `database/si_bantal.sql`

## Cara menjalankan

1. Extract ZIP ini.
2. Buat database MySQL/MariaDB:
   ```sql
   CREATE DATABASE si_bantal;
   ```
3. Sesuaikan `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=si_bantal
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Jalankan:
   ```bash
   php artisan migrate --seed
   php artisan serve
   ```
5. Buka:
   ```text
   http://127.0.0.1:8000
   ```

## Catatan penting

Migrasi ini dibuat sebagai tahap awal agar project lama sudah masuk ke Laravel dan dapat dijalankan lewat routing/controller Laravel. Beberapa bagian tampilan lama masih mempertahankan sintaks PHP di dalam Blade agar tampilan dan alur lama tetap sama. Untuk migrasi lanjutan yang lebih rapi, bagian berikut bisa ditingkatkan:

- Mengganti `mysqli_*` menjadi Eloquent Model.
- Mengganti session native `$_SESSION` menjadi Auth Laravel.
- Menambahkan `@csrf` di semua form, lalu mengaktifkan kembali CSRF penuh.
- Memecah `LegacyController` menjadi controller terpisah:
  - `AuthController`
  - `AdminController`
  - `ProgramController`
  - `DashboardController`
- Menambahkan validasi Laravel pada form upload dan input.
