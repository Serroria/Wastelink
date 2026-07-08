# TIECO - Platform Digital Bank Sampah Terintegrasi

TIECO adalah aplikasi web berbasis Laravel untuk mensimulasikan ekosistem bank sampah digital yang menghubungkan warga, operator bank sampah, UMKM mitra, dan pembeli industri. Aplikasi ini dirancang untuk mendukung pengelolaan sampah terpilah, insentif poin warga, katalog voucher UMKM, penjualan stok sampah B2B, settlement, dan laporan dampak lingkungan.

## Fitur Utama

- Dashboard publik dampak lingkungan dan ekonomi sirkular.
- Login demo multi-role: warga, bank sampah, UMKM, dan pembeli industri.
- Pengajuan setor sampah warga dengan foto, berat per jenis sampah, lokasi, dan jadwal.
- Verifikasi setoran oleh operator bank sampah dengan alur status operasional.
- Saldo poin warga, penarikan tunai, voucher UMKM, dan pembayaran tagihan simulatif.
- Katalog UMKM dan validasi voucher oleh mitra UMKM.
- Klaim settlement UMKM dan pembayaran settlement oleh bank sampah.
- Manajemen stok gudang dan listing penjualan sampah ke pembeli industri.
- Top up saldo kas virtual pembeli industri untuk simulasi transaksi B2B.
- Laporan dampak real-time untuk semua role.
- Proteksi route berbasis role.

## Teknologi

- PHP 8.3+
- Laravel 11
- SQLite untuk database lokal
- PHPUnit
- Laravel Pint
- Tailwind CSS via CDN untuk UI demo
- Chart.js via CDN untuk grafik laporan dampak

## Persyaratan Lokal

Pastikan extension PHP berikut aktif:

```bash
pdo_sqlite
sqlite3
pdo_mysql atau pdo_pgsql opsional jika ingin memakai database lain
```

Pada Laragon, extension SQLite dapat diaktifkan di file `php.ini` dengan membuka komentar:

```ini
extension=pdo_sqlite
extension=sqlite3
```

## Instalasi

Clone repository, lalu install dependency:

```bash
composer install
npm install
```

Salin environment:

```bash
copy .env.example .env
php artisan key:generate
```

Konfigurasi SQLite lokal pada `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=C:/laragon/www/ItClubProject/database/itclub.sqlite
```

Buat file database jika belum ada:

```bash
type nul > database\itclub.sqlite
```

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Jalankan aplikasi:

```bash
php artisan serve
```

Aplikasi dapat dibuka di:

```text
http://127.0.0.1:8000
```

## Akun Demo

Halaman login menyediakan pilihan role dan akan mengisi kredensial demo secara otomatis.

| Role | Identifier | Password |
| --- | --- | --- |
| Warga | 081234567890 | password |
| Bank Sampah | hendra@tieco.id | password |
| UMKM | sri@tieco.id | password |
| Pembeli Industri | daur@tieco.id | password |

## Alur Demo Singkat

1. Login sebagai warga, ajukan setor sampah, lalu cek dashboard warga.
2. Login sebagai bank sampah, verifikasi setoran sampai status disetujui.
3. Buka manajemen stok, buat listing sampah untuk marketplace B2B.
4. Login sebagai pembeli industri, top up saldo kas virtual, lalu beli listing.
5. Login sebagai warga, tukar poin menjadi voucher UMKM.
6. Login sebagai UMKM, validasi voucher dan ajukan settlement.
7. Login sebagai bank sampah, bayar settlement atau approve withdrawal.
8. Buka Laporan Dampak untuk melihat ringkasan dampak per role.

## Modul AI Deteksi Sampah

Halaman setor sampah memiliki modul Smart AI yang mencoba mendeteksi jenis sampah dari gambar. Backend AI diarahkan ke:

```text
http://127.0.0.1:5000/predict
```

Jika server AI tidak aktif, aplikasi tetap menyediakan fallback di sisi klien dan form setor tetap dapat digunakan.

## Testing

Jalankan seluruh test:

```bash
php artisan test --compact
```

Jalankan formatter PHP:

```bash
vendor/bin/pint --dirty --format agent
```

## Catatan Deployment

Untuk demo lomba, pastikan:

- File `.env` production tidak memakai kredensial demo sensitif.
- Extension SQLite aktif jika menggunakan SQLite.
- Database sudah dimigrasi dan di-seed bila perlu.
- Server AI lokal hanya dipakai sebagai modul opsional atau disiapkan terpisah.
- CDN Tailwind, Bootstrap Icons, dan Chart.js dapat diakses dari jaringan demo.
