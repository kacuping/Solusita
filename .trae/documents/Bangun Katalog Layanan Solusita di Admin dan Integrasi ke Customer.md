## Tujuan
- Membangun manajemen katalog layanan di `'/services'` (admin).
- Menampilkan layanan terkurasi di `'/customer/home'` dan daftar lengkap di `'/customer/services'` dengan detail/varian harga.

## Gambaran Teknis (Laravel + AdminLTE)
- Routing berbasis Laravel di `routes/web.php`.
- Area admin menggunakan Blade + AdminLTE; area pelanggan memakai Blade + Tailwind.
- Model utama: `App\Models\Service`; akan ditambah model varian harga untuk menampung skema pada daftar Anda.

## Struktur Data
- Tahan `Service` sebagai kategori/jenis (mis. "Home Cleaning", "Cuci Karpet", "Sofa").
- Tambah tabel `service_variants` untuk varian harga per layanan:
  - Kolom: `service_id`, `title`, `unit_type` (`per_jam`, `per_m2`, `per_unit`, `per_seater`, `per_liter`, dll), `price`, `min_units` (opsional), `notes` (opsional), `active`.
  - Model: `App\Models\ServiceVariant` (relasi `belongsTo Service`).
- Opsi ikon pada `Service` (`icon` string) agar konsisten di homepage pelanggan.

## Admin '/services'
- Tabel indeks: daftar `Service` + tombol "Kelola Varian".
- Form `Service` (buat/edit): `name`, `category`, `description`, `icon`, `active`.
- Halaman varian (nested resource): CRUD `ServiceVariant` untuk satu `Service`.
- Validasi:
  - `unit_type` wajib; `price` numerik; `min_units` untuk tipe durasi/jam.
- Menu AdminLTE: tambah entry `Services` menuju `route('services.index')`.

## Integrasi '/customer/home'
- Grid layanan (6–8 item aktif) dengan `name`, `icon`, dan CTA `Lihat Detail` (`/customer/service/{slug}`).
- Halaman detail layanan:
  - Tampilkan deskripsi dan daftar varian dari `ServiceVariant`.
  - Komponen pemesanan sederhana: input jumlah unit (jam/m²/unit/seater/liter) + validasi `min_units` → total harga.
  - Tombol `Pesan` mengarah ke alur booking yang sudah ada (`Booking`), mengirim `service_id`, `variant_id`, `units`, `calculated_amount`.
- Daftar lengkap di `'/customer/services'` dengan filter berdasarkan `category`.

## Pemetaan Konten Layanan
- Buat `Service` untuk kategori berikut, lalu isi `ServiceVariant` sesuai daftar:
  - Home Cleaning Durasi: 3 varian (1/2/3 cleaner `per_jam`, min 2 jam).
  - Toren per unit: 3 varian (<=1000L, 1000–1600L, 2000L `per_unit`).
  - Bed Putih/Cream: Small/Medium/Big/Full Busa `per_unit`.
  - Bed Nonton Putih/Cream: No1/No2/No3/Single `per_unit`.
  - Sofa: Standar/Jumbo `per_seater`.
  - Jok Mobil: 150K per baris.
  - Cuci Karpet: Gulung/Malaysia-Turki/Bisa `per_m2`.
  - Pembersihan Outdoor: Cabut/ Potong rumput `per_m2`.
  - Cat Bangunan: `per_m2`.
  - Interior Mobil: 350K/400K/450K `per_unit` (tipe mobil).
  - Cuci AC: Split 100K, Cassette 350K `per_unit`.
  - Sofa Bed: Medium/Big `per_unit`.
  - Kursi Makan Kayu: 50K `per_seater`.

## Controller & Routes
- Admin:
  - `AdminServiceController` untuk `Service` (index/create/store/update/destroy).
  - `AdminServiceVariantController` (nested) untuk varian: `services/{service}/variants/*`.
  - Proteksi dengan middleware `can:services.manage`.
- Customer:
  - `CustomerHomeController@index` menampilkan layanan aktif (limit 6–8).
  - `CustomerServiceController@index` daftar lengkap + filter kategori.
  - `CustomerServiceController@show` detail + varian.

## View & UI
- Admin: table/list + modal/form mengikuti gaya AdminLTE.
- Customer: kartu layanan (icon + nama), halaman detail dengan tabel varian dan kalkulator harga sederhana.

## Seeder (opsional)
- `DefaultServicesSeeder` isi data awal sesuai daftar untuk mempermudah uji.

## Pengujian & Verifikasi
- Unit test model relasi `Service`–`ServiceVariant` dan validasi harga.
- Feature test routing admin & customer, termasuk perhitungan total dengan `min_units`.
- Manual test: buat layanan di admin, pastikan tampil di `'/customer/home'`, detail, dan alur booking menghitung total dengan benar.

## Deliverables
- Migrasi + model `ServiceVariant`.
- Admin CRUD `Service` dan `ServiceVariant`.
- Tampilan customer (home, list, detail) terhubung ke varian.
- Seeder contoh.

Silakan konfirmasi. Setelah disetujui, saya akan mengimplementasikan skema data, halaman admin, dan integrasi di sisi pelanggan sesuai rencana di atas.