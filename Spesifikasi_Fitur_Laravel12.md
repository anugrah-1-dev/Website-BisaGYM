# Spesifikasi Fitur Aplikasi Gym (Panduan Rebuild ke Laravel 12 & MySQL)

Dokumen ini berisi penjelasan detail mengenai fitur-fitur yang ada pada aplikasi Gym saat ini (berbasis Java Desktop & MongoDB) untuk dipindahkan atau dibangun ulang menggunakan **Laravel 12** dengan database **MySQL**. Fitur Parkir telah dihapus sesuai permintaan.

---

## 1. Sistem Autentikasi & Otorisasi (User Management)
Sistem membedakan akses berdasarkan peran (Role-Based Access Control).
*   **Roles Utama:**
    *   **Admin:** Memiliki hak akses penuh. Dapat melihat laporan keuangan, mengelola paket gym, menghapus data, dan melihat riwayat/log seluruh aktivitas.
    *   **Penjaga (Staff Kasir/Resepsionis):** Bertugas dalam operasional harian seperti registrasi member, transaksi gym & snack, serta absensi.
*   **Active Sessions:** Sistem melacak waktu login (`login_time`) dan status sesi (`active` atau `inactive`) untuk mengetahui penjaga mana yang sedang bertugas, sehingga nama penjaga otomatis tercatat di setiap transaksi yang terjadi pada sesi tersebut.

## 2. Manajemen Member (Member Management)
Meliputi siklus hidup member dari pendaftaran hingga perpanjangan.
*   **Pendaftaran Member Baru & Validasi Ketat:**
    *   Data yang dicatat: Nama lengkap (min. 3 karakter), Tempat & Tanggal Lahir (sistem otomatis menghitung umur, minimal 5 tahun), Jenis Kelamin, NIK (wajib 16 digit angka), Pekerjaan (opsional jika di luar pilihan), Alamat Domisili, No. HP/WA, Email, dan Foto Diri (mendukung input dari *Webcam*).
    *   Format ID VIP khusus berbasis Timestamp, misal: `VIP-YYYYMMDD-HHMMSS-XXXX`.
*   **Sistem Early Registration (Pendaftaran Awal):**
    *   Aplikasi memiliki fitur deteksi "Tanggal Pembukaan Gym" (misal 1 Desember 2025). Jika member mendaftar sebelum tanggal tersebut, statusnya *Pending/Early Registration*, dan durasi keanggotaannya baru akan dihitung mulai dari tanggal pembukaan.
*   **Tipe Member & Paket:**
    *   Sistem mendukung berbagai opsi paket: 1 bulan, 3 bulan, 6 bulan, dan `paket_couple_1_bulan`.
*   **Status Keanggotaan:**
    *   Sistem menghitung dan memberikan peringatan sisa hari sebelum member kedaluwarsa (Expired). Member memiliki status *Active*, *Pending*, atau *Expired*.
*   **Update & Perpanjangan:**
    *   Tersedia fitur mengubah data diri member dan melakukan perpanjangan (renewal) paket gym tanpa harus mendaftar dari awal.
*   **Kartu Member & QR Code (E-Card):**
    *   Sistem secara otomatis meng-generate Kartu Member digital yang berisi Identitas lengkap, Foto Diri, masa berlaku, dan sebuah **QR Code** / **Barcode** yang digunakan untuk keperluan Absensi otomatis menggunakan Scanner/Webcam.

## 3. Sistem Absensi Member (Member Attendance)
Mencatat kedatangan harian member ke dalam area Gym.
*   **Metode Absensi:**
    *   **Manual:** Menggunakan input Member ID atau Nama.
    *   **Scan Barcode/QR:** Menggunakan *Webcam Service* untuk membaca kartu member secara otomatis.
*   **Validasi Kehadiran:** Sistem memvalidasi apakah member tersebut aktif. Jika *expired* atau belum aktif (karena mendaftar sebelum tanggal pembukaan), absensi akan ditolak.
*   **Statistik Absensi:** Sistem dapat menampilkan statistik kehadiran per member (Total Bulan Ini, Total Minggu Ini, Total Keseluruhan, dan Rata-rata Kunjungan).

## 4. Transaksi Pembayaran & Manajemen Paket Gym
Sistem dinamis untuk mengatur tarif dan mencatat pemasukan khusus dari keanggotaan.
*   **Manajemen Paket (Package Management):** 
    Admin dapat menambah, mengedit, atau menghapus paket. Atribut paket meliputi:
    *   Nama Paket
    *   Durasi (dalam angka)
    *   Satuan Durasi (Hari, Bulan, Tahun)
    *   Harga (Rp)
    *   Kategori (Member, Non-Member, Couple)
*   **Pencatatan Transaksi:** Setiap pendaftaran baru maupun perpanjangan dicatat ke dalam log transaksi (`transactions`) yang menyimpan data nominal, waktu bayar, paket yang dipilih, serta ID Penjaga/Kasir yang melayani.

## 5. POS & Manajemen Snack (Snack & Stock Management)
Modul untuk penjualan makanan, minuman, dan suplemen (selain keanggotaan).
*   **Data Snack (Inventaris):** Mengelola daftar barang yang memiliki atribut Harga Modal, Harga Jual, Jumlah Stok, dan Kategori.
*   **Transaksi Snack:** Modul kasir terpisah yang mencatat pembelian barang. Ketika transaksi berhasil (Checkout), stok barang otomatis berkurang dan dicatat ke `transaksi_snack`.

## 6. Laporan & Dashboard (Reporting)
*   **Dashboard Interaktif:** Menampilkan informasi harian *real-time* seperti waktu server saat ini, identitas penjaga yang sedang aktif, dan statistik harian (jumlah member aktif, pendapatan, dan total kunjungan hari ini).
*   **Export Laporan:** Terdapat modul pengekspor data (menggunakan kelas `ReportExporter`) untuk mencetak riwayat transaksi, data member, dan absensi ke format *Excel* atau *PDF*.

---

## Rekomendasi Struktur Database (MySQL)

Berikut adalah rancangan tabel MySQL yang diperbarui dan lebih komprehensif, disesuaikan dengan kode kontroler Laravel 12:

1.  **`users`**
    *   `id`, `name`, `username`, `password`, `role` (enum: 'admin', 'penjaga'), `last_login_at` (datetime), `is_active` (boolean), `timestamps`
2.  **`gym_packages`**
    *   `id`, `name`, `duration` (int), `duration_unit` (enum: 'hari', 'bulan', 'tahun'), `price` (decimal), `category` (enum: 'member', 'non-member', 'couple'), `is_active` (boolean), `timestamps`
3.  **`members`**
    *   `id`, `member_id` (string unik, misal VIP-20251201-143000-1234), `member_type`, `name`, `place_of_birth`, `date_of_birth` (date), `gender` (enum), `nik` (char 16), `job`, `address`, `phone`, `email`, `photo_path`, `registration_date` (datetime), `activation_date` (date - bisa diset ke masa depan jika early reg), `expiry_date` (date), `status` (enum: active, pending, expired), `extension_count` (int), `timestamps`
4.  **`member_attendances`**
    *   `id`, `member_id` (foreign key), `attendance_time` (datetime), `notes` (string/keterangan manual), `timestamps`
5.  **`member_transactions`**
    *   `id`, `transaction_code`, `member_id` (foreign key), `package_id` (foreign key), `user_id` (foreign key - kasir penjaga), `amount` (decimal), `transaction_date` (datetime), `transaction_type` (enum: 'new', 'renewal'), `timestamps`
6.  **`snacks`**
    *   `id`, `snack_code`, `name`, `category`, `stock` (int), `capital_price` (decimal), `selling_price` (decimal), `timestamps`
7.  **`snack_transactions`**
    *   `id`, `transaction_code`, `user_id` (foreign key - kasir), `total_amount` (decimal), `transaction_date` (datetime), `timestamps`
8.  **`snack_transaction_details`**
    *   `id`, `snack_transaction_id` (foreign key), `snack_id` (foreign key), `quantity` (int), `price_at_time` (decimal - harga saat transaksi), `subtotal` (decimal), `timestamps`

### Saran Implementasi Tambahan di Laravel 12:
*   Gunakan **Carbon** bawaan Laravel untuk memanipulasi perhitungan umur, validasi tanggal pendaftaran awal (Early Registration), serta notifikasi sisa hari member sebelum expired.
*   Buat **Form Requests** terpisah untuk validasi pembuatan akun member, misal pengecekan khusus format NIK (16 digit) dan batasan umur 5 tahun secara otomatis.
*   Gunakan **Spatie Laravel Permission** untuk mengelola role admin vs penjaga jika peran kedepannya berkembang lebih dari 2 jenis.
