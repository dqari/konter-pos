# 📱 Konter POS V2 - WM Celullar

Sistem Manajemen Point of Sale (POS) dan Inventori Cerdas berbasis Web, didesain khusus untuk toko retail Handphone dan Aksesoris.

Akun owner harus dibuat melalui proses provisioning database yang aman. Jangan menyimpan password default di source code atau dokumentasi produksi.

saat sudah di akun admin bisa membuat akun baru dengan role kasir atau manager Toko

## ✨ Fitur Unggulan
- **Dual Inventory System**: Pencatatan spesifik menggunakan IMEI (Handphone) dengan sistem FIFO, dan QTY (Aksesoris).
- **Smart Cashier Scanner**: Otomatis mendeteksi input barcode aksesoris maupun IMEI handphone dalam satu kolom pencarian (Scanner-Ready).
- **Merge-Cell Financial Report**: Laporan keuangan standar akuntansi dengan fitur *Rowspan* (penggabungan baris otomatis berdasarkan nomor struk/invoice).
- **Auto-Calculate Laba/Rugi**: Menghitung keuntungan bersih secara dinamis dari harga beli (modal) spesifik setiap barang yang terjual.
- **VBS Silent Start**: Fitur *buka/tutup konter* dengan sekali klik tanpa memunculkan jendela Command Prompt (DOS) XAMPP yang mengganggu.
- **Premium UI/UX Branding**: Desain *Midnight Blue* elegan dengan sentuhan emas khas WM Celullar. Responsif dan ringan dengan Bootstrap 5.

## 🛠️ Teknologi yang Digunakan
- **Backend Framework**: CodeIgniter 4 (PHP 8.2+)
- **Database**: MySQL / MariaDB (XAMPP)
- **Frontend**: Bootstrap 5, CSS3, HTML5
- **Visualisasi Data**: Chart.js & DataTables
- **System Scripting**: VBScript (.vbs) untuk *Invisible Background Task* di Windows.

## 🚀 Panduan Instalasi (Pemindahan ke Komputer Baru)

1. **Pindahkan Source Code**: Salin folder aplikasi `konter-pos` ke dalam folder `C:\xampp\htdocs\`.
2. **Setup XAMPP**: Buka folder `C:\xampp`, klik dua kali file `setup_xampp.bat` untuk me-refresh konfigurasi alamat direktori XAMPP di komputer baru.
3. **Konfigurasi Port Apache (Jika Port 80 Bentrok)**:
   - Buka `xampp-control.exe`.
   - Klik tombol **Config -> Apache (httpd.conf)**. Cari `Listen 80` dan ubah menjadi `Listen 8080`.
   - Klik tombol **Config -> Apache (httpd-ssl.conf)**. Cari `Listen 443` dan ubah menjadi `Listen 4433`.
   - Klik **Start** pada modul Apache dan MySQL.
4. **Setup Database**: 
   - Buka browser dan akses `http://localhost:8080/phpmyadmin`
   - Buat database baru (misal: `konter_pos`).
   - *Import* file database `.sql` yang telah di-backup dari komputer sebelumnya.
5. **Matikan Mode Development**:
   - Buka file `.env` di folder utama aplikasi.
   - Ubah baris `CI_ENVIRONMENT = development` menjadi `CI_ENVIRONMENT = production`.
6. **Jalankan migration database**:
   - Pastikan MySQL/MariaDB aktif.
   - Jalankan `php spark migrate` dari folder aplikasi.
   - Migration akan menambahkan biaya modal pada detail transaksi, status transaksi, dan tabel audit log.
   - Backup database sebelum migration dan verifikasi hasilnya melalui `php spark migrate:status`.

## Modul Operasional Tambahan

- **Retur Transaksi**: Owner/Manager dapat membuka menu Retur Transaksi. IMEI dikembalikan ke status Tersedia, aksesori dikembalikan sebagai batch stok baru, dan transaksi tetap tersimpan sebagai histori.
- **Stock Opname**: Owner/Manager dapat mencocokkan stok fisik dengan stok sistem. Selisih aksesori disesuaikan otomatis; selisih handphone perlu pemeriksaan IMEI manual.
- **Audit Aktivitas**: Owner dapat melihat aktivitas checkout, perubahan produk/stok/user, retur, opname, backup, dan perubahan profil toko.
- **Backup Manual**: Owner dapat membuka menu Backup Data dan mengunduh file SQL dari `writable/backups`.
- **Backup Otomatis Harian**: Command yang tersedia adalah `php spark backup:database`. Script siap pakai tersedia di `backup_database_daily.bat`; jadwalkan file ini di Windows Task Scheduler setiap hari. Backup tersimpan di `writable/backups`.
- **Validasi Harga**: Harga jual dan modal di atas Rp50.000.000 ditolak oleh server dan diberi peringatan pada form.

## 💡 Panduan Shortcut Desktop (VBS)
Agar kasir tidak perlu repot membuka XAMPP secara manual, gunakan dua script sakti ini (letakkan shortcut-nya di Desktop):
- **`Buka_Konter.vbs`**: Akan menjalankan Apache & MySQL secara *silent* di latar belakang, lalu otomatis membuka layar kasir di mode Microsoft Edge App.
  *(Pastikan script URL di dalamnya sudah menggunakan `:8080` jika Anda mengubah port).*
- **`Tutup_Toko.vbs`**: Menutup paksa browser layar kasir dan mematikan servis XAMPP dengan bersih dari memori komputer.

## 🔐 Hak Akses Default
- **Owner (Pemilik Toko)**: Memiliki akses penuh ke Dashboard, Inventori, Kasir, Manajemen User, Profil Toko, dan Laporan Keuangan Laba/Rugi.
- **Kasir**: Hanya memiliki akses ke layar Transaksi POS.

---
*Developed as a bespoke enterprise solution for WM Celullar.*