# Software-SPMI

## Deskripsi Proyek
Proyek ini adalah aplikasi yang dibangun menggunakan framework Laravel dan Vue.js, dirancang untuk memudahkan pengelolaan data dan laporan.

## Persyaratan
- PHP versi 8.2 atau lebih tinggi (https://www.php.net/downloads)
- Composer untuk manajemen dependensi PHP (https://getcomposer.org/download/)
- Node.js dan npm untuk manajemen dependensi JavaScript (https://nodejs.org/en/download/)
- Paket `phpoffice/phpspreadsheet` untuk manipulasi spreadsheet

## Langkah Awal

ikuti langkah-langkah berikut sebelum menjalankan proyek Laravel:

1. **Clone Repository**:
   Gunakan perintah berikut untuk meng-clone repository:
   ```bash
   git clone https://github.com/Burzess/Software-SPMI.git
   ```

2. **Masuk ke Direktori Proyek**:
   ```bash
   cd Software-SPMI
   ```

3. **Salin File `.env`**:
   Jika file `.env` belum ada, salin file contoh `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```

4. **Instal Dependensi PHP**:
   Jalankan perintah berikut untuk menginstal dependensi PHP:
   ```bash
   composer install
   ```

5. **Instal Dependensi JavaScript**:
   Setelah menginstal dependensi PHP, jalankan:
   ```bash
   npm install
   ```

6. **Generate Kunci Aplikasi**:
   Jalankan perintah berikut untuk menghasilkan kunci aplikasi:
   ```bash
   php artisan key:generate
   ```

7. **Konfigurasi Database**:
   Edit file `.env` untuk mengonfigurasi pengaturan database sesuai kebutuhan proyek Anda.

8. **Migrasi Database**:
   Jalankan perintah berikut untuk melakukan migrasi database:
   ```bash
   php artisan migrate
   ```

## Menjalankan Aplikasi

1. **Menjalankan Server Laravel**:
   ```bash
   php artisan serve
   ```

2. **Menjalankan Server Pengembangan Vite**:
   ```bash
   npm run dev
   ```


## Catatan
- Pastikan untuk mengonfigurasi file `.env` sesuai kebutuhan proyek.
- Untuk migrasi database, jalankan:
  ```bash
  php artisan migrate
  ```