<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WasteType;
use App\Models\WasteDeposit;
use App\Models\UmkmPartner;
use App\Models\UmkmProduct;
use App\Models\Voucher;
use App\Models\Withdrawal;
use App\Models\WasteListing;
use App\Models\Settlement;
use App\Models\SystemStat;
use Illuminate\Support\Str;

class TiecoSeeder extends Seeder
{
    public function run(): void
    {
        // ===== PENGGUNA DEMO =====
        $warga = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_santoso',
            'email' => 'budi@tieco.id',
            'password' => 'password',
            'role' => 'warga',
            'phone' => '081234567890',
            'address' => 'Jl. Tuparev No. 12, RT 03/RW 07, Karawang',
            'point_balance' => 2850,
            'cash_balance' => 0,
        ]);

        $warga2 = User::create([
            'name' => 'Siti Rahayu',
            'username' => 'siti_rahayu',
            'email' => 'siti@tieco.id',
            'password' => 'password',
            'role' => 'warga',
            'phone' => '081298765432',
            'address' => 'Jl. Kertabumi No. 5, RT 02/RW 07, Karawang',
            'point_balance' => 1200,
            'cash_balance' => 0,
        ]);

        $warga3 = User::create([
            'name' => 'Ahmad Fauzi',
            'username' => 'ahmad_fauzi',
            'email' => 'ahmad@tieco.id',
            'password' => 'password',
            'role' => 'warga',
            'phone' => '081355667788',
            'address' => 'Jl. Galuh Mas Raya No. 8, RT 01/RW 07, Karawang',
            'point_balance' => 750,
            'cash_balance' => 0,
        ]);

        $operator = User::create([
            'name' => 'Pak Hendra (Operator BS)',
            'username' => 'hendra_operator',
            'email' => 'hendra@tieco.id',
            'password' => 'password',
            'role' => 'bank_sampah',
            'phone' => '081377889900',
            'address' => 'Bank Sampah Lestari Karawang, Jl. Tuparev No. 1, RT 01/RW 07, Karawang',
            'point_balance' => 0,
            'cash_balance' => 0,
        ]);

        $pembeli = User::create([
            'name' => 'PT Daur Mulia Sejahtera',
            'username' => 'daur_mulia',
            'email' => 'daur@tieco.id',
            'password' => 'password',
            'role' => 'pembeli',
            'phone' => '02155667788',
            'address' => 'Kawasan Industri Pulogadung, Jakarta Timur',
            'point_balance' => 0,
            'cash_balance' => 5000000,
        ]);

        // ===== JENIS SAMPAH & TARIF =====
        $plastik = WasteType::create([
            'name' => 'Plastik',
            'icon' => '♻️',
            'points_per_kg' => 150,
            'price_per_kg' => 3500,
        ]);

        $kertas = WasteType::create([
            'name' => 'Kertas & Kardus',
            'icon' => '📦',
            'points_per_kg' => 100,
            'price_per_kg' => 2000,
        ]);

        $logam = WasteType::create([
            'name' => 'Logam & Kaleng',
            'icon' => '🔩',
            'points_per_kg' => 250,
            'price_per_kg' => 8000,
        ]);

        $kaca = WasteType::create([
            'name' => 'Kaca & Botol',
            'icon' => '🫙',
            'points_per_kg' => 120,
            'price_per_kg' => 1500,
        ]);

        $elektronik = WasteType::create([
            'name' => 'Elektronik (E-Waste)',
            'icon' => '🔌',
            'points_per_kg' => 500,
            'price_per_kg' => 15000,
        ]);

        $minyakJelantah = WasteType::create([
            'name' => 'Minyak Jelantah',
            'icon' => '🛢️',
            'points_per_kg' => 200,
            'price_per_kg' => 5000,
        ]);

        // ===== DEPOSIT SAMPAH (RIWAYAT) =====
        // Deposit sudah disetujui
        WasteDeposit::create([
            'user_id' => $warga->id,
            'collector_id' => $operator->id,
            'status' => 'approved',
            'collection_method' => 'jemput',
            'schedule_date' => now()->subDays(14),
            'address' => $warga->address,
            'weight_details' => json_encode([$plastik->id => 3.2, $kertas->id => 5.0]),
            'total_points' => 980,
            'photo_proof' => null,
            'notes' => 'Sampah terpilah dengan baik.',
        ]);

        WasteDeposit::create([
            'user_id' => $warga->id,
            'collector_id' => $operator->id,
            'status' => 'approved',
            'collection_method' => 'antar',
            'schedule_date' => now()->subDays(7),
            'address' => null,
            'weight_details' => json_encode([$logam->id => 2.0, $kaca->id => 1.5]),
            'total_points' => 680,
            'photo_proof' => null,
            'notes' => 'Kaleng aluminium dan botol kaca.',
        ]);

        WasteDeposit::create([
            'user_id' => $warga2->id,
            'collector_id' => $operator->id,
            'status' => 'approved',
            'collection_method' => 'jemput',
            'schedule_date' => now()->subDays(10),
            'address' => $warga2->address,
            'weight_details' => json_encode([$kertas->id => 8.0, $plastik->id => 4.0]),
            'total_points' => 1400,
            'photo_proof' => null,
        ]);

        // Deposit menunggu verifikasi
        WasteDeposit::create([
            'user_id' => $warga->id,
            'collector_id' => null,
            'status' => 'pending',
            'collection_method' => 'jemput',
            'schedule_date' => now()->addDays(2),
            'address' => $warga->address,
            'weight_details' => json_encode([$plastik->id => 5.0, $elektronik->id => 1.0]),
            'total_points' => 0,
            'notes' => 'Ada 1 kipas angin rusak dan plastik botol.',
        ]);

        WasteDeposit::create([
            'user_id' => $warga3->id,
            'collector_id' => null,
            'status' => 'pending',
            'collection_method' => 'antar',
            'schedule_date' => now()->addDays(1),
            'address' => null,
            'weight_details' => json_encode([$minyakJelantah->id => 3.0, $kertas->id => 2.0]),
            'total_points' => 0,
            'notes' => 'Minyak jelantah dikemas di botol bekas air mineral.',
        ]);

        // Deposit ditolak
        WasteDeposit::create([
            'user_id' => $warga2->id,
            'collector_id' => $operator->id,
            'status' => 'rejected',
            'collection_method' => 'antar',
            'schedule_date' => now()->subDays(5),
            'weight_details' => json_encode([$plastik->id => 1.0]),
            'total_points' => 0,
            'notes' => 'Sampah tidak terpilah, tercampur organik basah.',
        ]);

        // ===== MITRA UMKM (10 Rekomendasi di Karawang) =====
        $umkmPartnersData = [
            [
                'name' => 'Bu Sri',
                'email' => 'sri@tieco.id',
                'store_name' => 'Warung Berkah Bu Sri',
                'category' => 'Sembako',
                'address' => 'Jl. Tuparev No. 15, RT 02/RW 01, Karawang',
                'description' => 'Warung sembako lengkap menyediakan beras, minyak goreng, gula, telur, dan kebutuhan sehari-hari.',
                'latitude' => -6.3032,
                'longitude' => 107.3055,
                'products' => [
                    ['name' => 'Beras Premium 1 kg', 'desc' => 'Beras pandan wangi kualitas premium.', 'points' => 800, 'val' => 14000, 'stock' => 25],
                    ['name' => 'Minyak Goreng 1 Liter', 'desc' => 'Minyak goreng sawit kemasan pouch.', 'points' => 1000, 'val' => 18000, 'stock' => 30],
                    ['name' => 'Gula Pasir 500 gram', 'desc' => 'Gula pasir putih lokal.', 'points' => 500, 'val' => 8500, 'stock' => 40],
                    ['name' => 'Telur Ayam 1/2 kg', 'desc' => 'Telur ayam negeri segar.', 'points' => 600, 'val' => 13000, 'stock' => 20],
                ]
            ],
            [
                'name' => 'Pak Joko',
                'email' => 'joko@tieco.id',
                'store_name' => 'Kreatif Daur Ulang Pak Joko',
                'category' => 'Kerajinan Daur Ulang',
                'address' => 'Jl. Kertabumi No. 12, RT 01/RW 02, Karawang',
                'description' => 'Menjual kerajinan tangan dari bahan daur ulang: pot bunga botol bekas, tas eco-bag, dan hiasan dinding.',
                'latitude' => -6.3045,
                'longitude' => 107.3080,
                'products' => [
                    ['name' => 'Pot Bunga Botol Bekas', 'desc' => 'Pot bunga cantik terbuat dari botol plastik daur ulang.', 'points' => 300, 'val' => 5000, 'stock' => 15],
                    ['name' => 'Eco-Bag Tas Belanja', 'desc' => 'Tas belanja ramah lingkungan dari kain perca.', 'points' => 700, 'val' => 12000, 'stock' => 10],
                    ['name' => 'Hiasan Dinding Mosaik', 'desc' => 'Hiasan dinding dari pecahan kaca.', 'points' => 1500, 'val' => 35000, 'stock' => 5],
                ]
            ],
            [
                'name' => 'Haji Salim',
                'email' => 'salim@tieco.id',
                'store_name' => 'Toko Kelontong Barokah',
                'category' => 'Sembako',
                'address' => 'Jl. Interchange Karawang Barat No. 8, Karawang',
                'description' => 'Menyediakan aneka kebutuhan pokok, sabun, sampo, makanan ringan, dan minuman dingin.',
                'latitude' => -6.3120,
                'longitude' => 107.2920,
                'products' => [
                    ['name' => 'Sabun Mandi Cair 400ml', 'desc' => 'Sabun mandi keluarga antiseptik.', 'points' => 1200, 'val' => 22000, 'stock' => 15],
                    ['name' => 'Mie Instan Karton', 'desc' => 'Satu karton mie instan rasa kaldu ayam.', 'points' => 5000, 'val' => 95000, 'stock' => 8],
                ]
            ],
            [
                'name' => 'Neng Aura',
                'email' => 'aura@tieco.id',
                'store_name' => 'EcoArt Studio Telukjambe',
                'category' => 'Kerajinan Daur Ulang',
                'address' => 'Jl. Raya Telukjambe No. 34, Karawang',
                'description' => 'Produk interior dan dekorasi rumah ramah lingkungan dari kertas semen dan kayu bekas palet.',
                'latitude' => -6.3250,
                'longitude' => 107.3010,
                'products' => [
                    ['name' => 'Bingkai Foto Kayu Palet', 'desc' => 'Bingkai foto estetik ukuran 10R.', 'points' => 2000, 'val' => 45000, 'stock' => 12],
                ]
            ],
            [
                'name' => 'Kang Asep',
                'email' => 'asep@tieco.id',
                'store_name' => 'Mart Sayur Segar Galuh Mas',
                'category' => 'Sembako',
                'address' => 'Jl. Galuh Mas Raya No. 55, Karawang',
                'description' => 'Menjual aneka sayur mayur organik segar, bumbu dapur instan, dan buah-buahan lokal langsung dari petani.',
                'latitude' => -6.3180,
                'longitude' => 107.2990,
                'products' => [
                    ['name' => 'Paket Sayur Sup Lengkap', 'desc' => 'Paket sayuran siap masak untuk sup hangat.', 'points' => 400, 'val' => 7000, 'stock' => 20],
                    ['name' => 'Pisang Raja 1 Sisir', 'desc' => 'Pisang raja manis matang pohon.', 'points' => 1200, 'val' => 25000, 'stock' => 10],
                ]
            ],
            [
                'name' => 'Teh Rina',
                'email' => 'rina@tieco.id',
                'store_name' => 'Kedai Kopi Lokal Karawang',
                'category' => 'Kuliner',
                'address' => 'Jl. Tarumanegara No. 8, Karawang',
                'description' => 'Warung kopi saring tradisional dengan biji kopi Robusta Karawang asli pilihan.',
                'latitude' => -6.3150,
                'longitude' => 107.2880,
                'products' => [
                    ['name' => 'Kopi Bubuk Asli 250g', 'desc' => 'Robusta Karawang sangrai premium.', 'points' => 2500, 'val' => 50000, 'stock' => 15],
                ]
            ],
            [
                'name' => 'Pak Slamet',
                'email' => 'slamet@tieco.id',
                'store_name' => 'Sanggar Bambu Hias Klari',
                'category' => 'Kerajinan Daur Ulang',
                'address' => 'Jl. Raya Klari No. 110, Karawang',
                'description' => 'Kerajinan anyaman bambu premium, wadah tisu, tudung saji, dan dekorasi lampu tidur.',
                'latitude' => -6.3380,
                'longitude' => 107.3520,
                'products' => [
                    ['name' => 'Kotak Tisu Anyaman Bambu', 'desc' => 'Wadah tisu ramah lingkungan buatan tangan.', 'points' => 1800, 'val' => 40000, 'stock' => 14],
                ]
            ],
            [
                'name' => 'Ibu Lilis',
                'email' => 'lilis@tieco.id',
                'store_name' => 'Kedai Makanan Organik Rengasdengklok',
                'category' => 'Kuliner',
                'address' => 'Jl. Raya Rengasdengklok No. 42, Karawang',
                'description' => 'Menyediakan catering sehat, nasi merah bakar organik, sayur lodeh bebas MSG, dan jus herbal.',
                'latitude' => -6.1620,
                'longitude' => 107.2980,
                'products' => [
                    ['name' => 'Voucher Makan Sehat Rp20rb', 'desc' => 'Voucher potongan harga makan di kedai.', 'points' => 1500, 'val' => 20000, 'stock' => 50],
                ]
            ],
            [
                'name' => 'Hendra Lestari',
                'email' => 'tani@tieco.id',
                'store_name' => 'Toko Tani Lestari Cikampek',
                'category' => 'Sembako',
                'address' => 'Jl. Jenderal Sudirman No. 89, Karawang',
                'description' => 'Menyediakan benih tanaman, media tanam, dan pupuk kompos organik hasil daur ulang sampah.',
                'latitude' => -6.4060,
                'longitude' => 107.4580,
                'products' => [
                    ['name' => 'Pupuk Kompos Organik 5kg', 'desc' => 'Kompos berkualitas tinggi menyuburkan tanah.', 'points' => 800, 'val' => 15000, 'stock' => 30],
                ]
            ],
            [
                'name' => 'Apotek Karawang',
                'email' => 'sehat@tieco.id',
                'store_name' => 'Mitra Sehat Karawang Kulon',
                'category' => 'Kesehatan',
                'address' => 'Jl. Surotokunto No. 27, Karawang',
                'description' => 'Penyedia obat bebas, vitamin, suplemen kesehatan, masker medis, dan alat kesehatan dasar.',
                'latitude' => -6.3110,
                'longitude' => 107.3290,
                'products' => [
                    ['name' => 'Masker Medis 3-Ply 1 Box', 'desc' => 'Satu box isi 50 pcs masker kesehatan kualitas tinggi.', 'points' => 1500, 'val' => 30000, 'stock' => 20],
                    ['name' => 'Vitamin C 500mg (Satu Strip)', 'desc' => 'Suplemen daya tahan tubuh keluarga.', 'points' => 500, 'val' => 10000, 'stock' => 50],
                ]
            ]
        ];

        $umkm1 = null;
        $umkm2 = null;

        foreach ($umkmPartnersData as $index => $data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => Str::slug(Str::before($data['email'], '@'), '_'),
                'email' => $data['email'],
                'password' => 'password',
                'role' => 'umkm',
                'phone' => '0813' . rand(10000000, 99999999),
                'address' => $data['address'],
                'point_balance' => 0,
                'cash_balance' => 0,
            ]);

            $partner = UmkmPartner::create([
                'user_id' => $user->id,
                'store_name' => $data['store_name'],
                'category' => $data['category'],
                'address' => $data['address'],
                'description' => $data['description'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            if ($index === 0) {
                $umkm1 = $partner;
            } elseif ($index === 1) {
                $umkm2 = $partner;
            }

            foreach ($data['products'] as $prod) {
                UmkmProduct::create([
                    'umkm_partner_id' => $partner->id,
                    'name' => $prod['name'],
                    'description' => $prod['desc'],
                    'points_cost' => $prod['points'],
                    'price_value' => $prod['val'],
                    'stock' => $prod['stock'],
                ]);
            }
        }

        // ===== VOUCHER (RIWAYAT) =====
        Voucher::create([
            'user_id' => $warga2->id,
            'umkm_product_id' => 1,
            'code' => 'TC-' . strtoupper(Str::random(8)),
            'points_spent' => 800,
            'status' => 'used',
            'used_at' => now()->subDays(3),
        ]);

        Voucher::create([
            'user_id' => $warga->id,
            'umkm_product_id' => 5,
            'code' => 'TC-' . strtoupper(Str::random(8)),
            'points_spent' => 300,
            'status' => 'unused',
        ]);

        // ===== PENARIKAN TUNAI =====
        Withdrawal::create([
            'user_id' => $warga->id,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Budi Santoso',
            'points_amount' => 500,
            'equivalent_rp' => 5000,
            'status' => 'approved',
        ]);

        Withdrawal::create([
            'user_id' => $warga3->id,
            'bank_name' => 'BRI',
            'account_number' => '9876543210',
            'account_name' => 'Ahmad Fauzi',
            'points_amount' => 300,
            'equivalent_rp' => 3000,
            'status' => 'pending',
        ]);

        // ===== LISTING B2B =====
        WasteListing::create([
            'title' => 'Plastik Campuran PET & HDPE - 45 kg',
            'description' => 'Plastik terpilah siap jual, dominan botol PET dan tutup HDPE. Sudah dicuci dan dipress.',
            'weight_details' => json_encode([$plastik->id => 45]),
            'total_price' => 157500,
            'status' => 'sold',
            'buyer_id' => $pembeli->id,
            'sold_at' => now()->subDays(5),
        ]);

        WasteListing::create([
            'title' => 'Kertas & Kardus Bekas - 80 kg',
            'description' => 'Kardus bekas pengiriman dan kertas HVS. Kondisi kering, belum dipress.',
            'weight_details' => json_encode([$kertas->id => 80]),
            'total_price' => 160000,
            'status' => 'available',
        ]);

        WasteListing::create([
            'title' => 'Logam Campuran (Aluminium & Besi) - 15 kg',
            'description' => 'Kaleng aluminium bekas minuman dan potongan besi kecil.',
            'weight_details' => json_encode([$logam->id => 15]),
            'total_price' => 120000,
            'status' => 'available',
        ]);

        // ===== SETTLEMENT =====
        Settlement::create([
            'umkm_partner_id' => $umkm1->id,
            'total_amount' => 14000,
            'voucher_ids' => json_encode([1]),
            'status' => 'paid',
            'paid_at' => now()->subDays(2),
        ]);

        // ===== STATISTIK SISTEM =====
        SystemStat::create([
            'bank_sampah_cash' => 143500, // dari hasil jual listing pertama 157500 - bayar settlement 14000
            'total_co2_saved' => 287.50, // estimasi: 1 kg sampah terkelola ≈ 2.5 kg CO2 tersimpan
            'total_landfill_saved' => 115.0,
        ]);
    }
}
