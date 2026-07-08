# TIECO

TIECO adalah sistem informasi bank sampah digital berbasis web yang menghubungkan warga, Bank Sampah, UMKM mitra, dan pembeli industri dalam satu alur ekonomi sirkular. Sistem ini membantu proses setor sampah terpilah, pencatatan poin warga, penukaran manfaat, pengelolaan stok gudang, penjualan sampah B2B, settlement UMKM, serta pelaporan dampak lingkungan dan sosial.

## Penjelasan Sistem

TIECO dirancang sebagai platform operasional untuk ekosistem pengelolaan sampah bernilai ekonomi. Warga dapat mengajukan penyetoran sampah, Bank Sampah memverifikasi timbangan dan mengelola stok, UMKM menyediakan produk atau voucher yang dapat ditukar dengan poin, sedangkan pembeli industri membeli stok sampah terpilah dari Bank Sampah.

Sistem memakai role-based access sehingga setiap pengguna hanya dapat mengakses fitur sesuai perannya. Alur transaksi utama tercatat di database agar saldo poin, saldo kas, stok gudang, listing B2B, dan laporan dampak tetap konsisten.

## Role Pengguna

- Warga: mengajukan setoran sampah, melihat poin, menukar voucher UMKM, mencairkan poin, dan membayar tagihan simulatif.
- Bank Sampah: memverifikasi setoran, mengelola stok, membuat listing B2B, memproses withdrawal, membayar settlement UMKM, serta menambahkan akun Bank Sampah internal.
- UMKM Mitra: mendaftarkan usaha, mengelola produk, memvalidasi voucher warga, dan mengajukan settlement.
- Pembeli Industri: melihat listing stok sampah, top up saldo kas virtual, dan membeli stok terpilah.

## Fitur Sistem

- Landing page laporan dampak publik.
- Login, register publik, dan login demo multi-role.
- Registrasi publik untuk Warga, UMKM, dan Pembeli Industri.
- Proteksi route berdasarkan role pengguna.
- Smart AI pada halaman setor sampah untuk membantu mengenali kategori sampah dari gambar atau kata kunci nama file.
- Form setor sampah dengan foto bukti, detail berat per jenis sampah, metode pengumpulan, jadwal, alamat, dan koordinat lokasi.
- Verifikasi setoran oleh Bank Sampah dengan update status dan penambahan poin warga.
- Saldo poin warga, pencairan poin, voucher UMKM, dan pembayaran tagihan simulatif.
- Manajemen UMKM mitra, produk UMKM, voucher, dan klaim settlement.
- Manajemen stok gudang berdasarkan setoran yang sudah disetujui.
- Listing penjualan sampah B2B ke pembeli industri.
- Top up saldo kas virtual untuk pembeli industri.
- Pembatalan listing stok B2B dengan pengembalian stok ke statistik gudang.
- Halaman laporan dampak yang dapat diakses dari semua role.
- Pengaturan profil pengguna dan foto profil.

## Requirement Sistem

- PHP 8.3 atau lebih baru.
- Composer.
- Node.js dan npm.
- SQLite untuk penggunaan lokal, atau database lain yang didukung Laravel jika dikonfigurasi ulang.
- Extension PHP:
  - `pdo_sqlite`
  - `sqlite3`
  - `fileinfo`
  - `mbstring`
  - `openssl`
  - `pdo`
  - `tokenizer`
  - `xml`
- Browser modern seperti Chrome, Edge, atau Firefox.
- Koneksi internet jika memakai CDN Tailwind, Bootstrap Icons, Chart.js, TensorFlow.js, MobileNet, Leaflet, dan reverse geocoding.

## Instalasi

Clone repository lalu masuk ke folder project:

```bash
git clone <url-repository>
cd ItClubProject
```

Install dependency backend dan frontend:

```bash
composer install
npm install
```

Salin file environment dan buat application key:

```bash
copy .env.example .env
php artisan key:generate
```

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Buka aplikasi di browser:

```text
http://127.0.0.1:8000
```

Jika frontend asset perlu dibundel, jalankan:

```bash
npm run build
```

Untuk mode pengembangan frontend:

```bash
npm run dev
```

## Akun Demo

Seeder menyediakan akun demo untuk mencoba alur sistem.

| Role | Identifier | Password |
| --- | --- | --- |
| Warga | 081234567890 | password |
| Bank Sampah | hendra@tieco.id | password |
| UMKM | sri@tieco.id | password |
| Pembeli Industri | daur@tieco.id | password |

## Modul Smart AI

Halaman setor sampah memiliki modul Smart AI untuk membantu mengenali jenis sampah. Deteksi dilakukan melalui beberapa lapisan:

- kata kunci nama file, misalnya `botol-plastik.jpg`, `kardus.jpg`, `kaleng.jpg`, `kaca.jpg`, atau `minyak-jelantah.jpg`;
- respons server AI eksternal jika tersedia;
- model MobileNet di browser sebagai fallback lokal;
- pilihan manual jika sistem belum yakin.

Endpoint server AI eksternal secara default diarahkan ke:

```text
http://127.0.0.1:5000/predict
```

Jika server AI tidak aktif, aplikasi tetap dapat digunakan karena deteksi fallback berjalan di sisi browser dan form setor tetap manual.

## Testing dan Formatting

Jalankan test:

```bash
php artisan test --compact
```

Format kode PHP:

```bash
vendor/bin/pint --dirty --format agent
```

## Catatan Konfigurasi

- Untuk SQLite di Laragon, aktifkan `extension=pdo_sqlite` dan `extension=sqlite3` pada `php.ini`.
- Jika memakai MySQL/PostgreSQL, ubah konfigurasi `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai server database.
- CDN eksternal perlu dapat diakses agar UI, grafik, peta, dan model AI browser tampil optimal.
