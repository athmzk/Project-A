
Langkah-Langkah Menjalankan Aplikasi Secara Lokal

Langkah 1: Persiapan File Proyek
1. Unduh atau Clone repositori GitHub ini.
2. Ekstrak folder talah.
3. bisa juga untuk memasukkan folder yang sudah saya bikin terpisah untuk fleksibilitas
4. Pindahkan folder `talah` tersebut ke dalam direktori server lokal XAMPP Anda:
   * Windows: `C:\xampp\htdocs\`

Langkah 2: Membuat Folder Penyimpanan Gambar
1. Buka folder `talah` yang ada di dalam direktori `htdocs`.
2. kemudian buka lagi folder project
3. Pastikan di dalam folder tersebut sudah terdapat folder bernama `uploads`Folder ini wajib ada untuk menampung file foto material yang diunggah oleh pengguna.

Langkah 3: Konfigurasi dan Import Database
1. Buka XAMPP Control Panel dan klik tombol Start pada modul Apache dan MySQL.
2. Buka aplikasi manajemen database Anda (SQLyog atau phpMyAdmin melalui alamat `http://localhost/phpmyadmin`).
3. Buat database baru dengan nama `spdatabase`.
4. Import file skrip SQL (struktur tabel) yang tersedia ke dalam database `spdatabase` tersebut.
5. Buka berkas `config.php` using code editor untuk memastikan pengaturan host, username (`root`), password (kosongkan jika default), dan nama database telah sesuai dengan localhost Anda.

Langkah 4: Menjalankan Aplikasi di Browser
1. Buka Web Browser pilihan Anda.
2. Akses URL berikut pada kolom pencarian:
   ```text
   http://localhost/talah/project/
