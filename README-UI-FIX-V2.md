# Timcare UI Fix V2 — Monitoring, Tim, dan Pengguna

## Untuk instalasi mana?
Pembaruan ini dipasang DI ATAS patch Monitoring TI sebelumnya yang sudah terpasang. Screenshot pengguna menunjukkan fitur tersebut sudah tersedia. Ini bukan paket instalasi Timcare baru. Gunakan struktur root Laravel yang sama dengan ZIP awal.

Tidak ada perubahan skema database, migrasi baru, impor, reset role, reset password, atau penghapusan laporan. Tidak membawa .env, vendor, node_modules, dump SQL, SQLite QA, file unggahan, atau skrip bash.

## Pemasangan — command langsung
1. Backup file aplikasi yang akan ditimpa. Jika source server telah diedit lagi, bandingkan perubahan terlebih dahulu. FILES-UI-FIX-V2.json mencatat hash versi patch sebelumnya dan hash file baru.
2. Masuk ke ROOT LARAVEL (folder berisi artisan, app, routes, resources, .env), lalu sebelum unzip:

```bash
php artisan down --retry=60
```

3. Ekstrak ZIP langsung ke root tersebut, izinkan overwrite. Jangan mengekstrak seluruh app/resources/routes hanya ke public_html bila folder aplikasi sebenarnya berbeda. Tidak ada folder tambahan di atas struktur ZIP.
4. Jalankan satu per satu:

```bash
php artisan optimize:clear
php artisan view:cache
php artisan up
```

Tidak perlu `php artisan migrate`, import tim, composer, npm, atau bash. Jika command gagal, jangan buka aplikasi kembali sebelum diperbaiki. Bila opcode cache hosting masih menyimpan kode lama, gunakan fitur reload PHP/OPcache dari hosting. Setelah selesai, lakukan refresh browser.

## Perbaikan utama
- Styling dan perilaku modul dimuat langsung dari partial Blade (`resources/views/ti/partials/assets.blade.php`), bukan hanya dari URL `public/css/timcare-ti.css`. Ini menghindari tampilan polos ketika public aplikasi dan document root hosting berbeda atau CSS tambahan belum tersalin.
- CSS publik tetap disertakan sebagai salinan sumber/kompatibilitas, tetapi halaman patch tidak bergantung pada URL itu. Base CSS/JS Timcare existing tetap dipakai; ini tidak mengganti seluruh frontend aplikasi.
- Form milik modul dan form user terkait memakai submit native Laravel. Handler AJAX global existing tidak lagi mengambil alih form tersebut; CSRF, redirect, flash, old input, dan error validasi tetap mengikuti alur Laravel.
- Indikator menyimpan dan pencegahan klik ganda. Tombol dipulihkan saat kembali dengan browser Back.
- Panel “Mulai pengisian laporan” dipisahkan dari panel filter pencarian. Hanya objek aktif yang boleh diedit oleh pengguna yang muncul pada pilihan pengisian.
- Dropdown filter semester diperbarui langsung ketika jenis formulir berganti, dan bulan yang tidak valid untuk semester dikosongkan. Server menolak semester di luar I/II.
- Keterangan update menjadi wajib di browser ketika Update fitur = Ya; validasi server tetap berlaku.
- Teks penuh pilihan skala panjang ditampilkan di bawah dropdown supaya tetap terbaca pada mobile.
- Error validasi berbahasa Indonesia untuk form monitoring/objek/tim, ditautkan ke kolom terkait, dan fokus diarahkan ke ringkasan error. Data isian tetap tersimpan saat validasi gagal.
- Error pada edit satu tim tidak menandai semua form tim lain atau memindahkan teks edit ke form tambah tim.
- Halaman Detail/Lihat User yang tidak ada pada source awal ditambahkan. Tampilan detail mencakup role, kontak, dan tim. Redirect setelah ubah password kini mempunyai tujuan yang tersedia.
- Form tambah/edit user, pilihan beberapa tim, pengosongan tim, pencarian user, dan ubah password diuji dengan submit nyata.
- Tabel user diberi ruang untuk nama tim panjang. Layout halaman baru responsif, mendukung light/dark, dan tidak membutuhkan build Tailwind ulang.

## Hak akses tetap
PIC dan ketua tim pengelola objek saja yang dapat membuat/edit laporan. Admin tidak otomatis memperoleh hak edit laporan; Admin tetap mengatur objek/PIC/tim. Anggota biasa hanya baca sesuai cakupan. Tim user tetap opsional, boleh kosong/multi-tim. Perbaikan UI tidak memperluas hak akses.

Jika panel pengisian tidak menampilkan objek, bukan berarti form rusak: periksa PIC dan tim pengelola serta ketua tim untuk objek aktif tersebut melalui akun Admin.

## Cakupan yang diperiksa
- Daftar laporan dan pengisian/edit/detail pemanfaatan bulanan, monitoring bulanan, tindak lanjut semesteran.
- Daftar/tambah/edit objek TI, PIC, dan tim pengelola.
- Tambah/edit tim kerja dan ketua.
- Daftar/cari/tambah/edit/detail pengguna dan ubah password.

Menu lama lain di luar perubahan patch—misalnya tiket, reservasi Zoom, aset, backup PDF, dan jadwal piket—tidak didesain ulang atau diklaim bebas error. Bila ada error di menu tersebut, kirim URL/nama menu, screenshot, dan pesan error agar ditangani pada sumber yang tepat.

## Hasil pengujian
- PHPUnit: 36 tes, 172 assertion, semuanya lulus.
- Browser monitoring: 36 pemeriksaan lulus, termasuk create/edit ketiga jenis laporan, required dinamis, validasi duplikasi dan old input, filter, CSV, PIC/ketua/anggota, dan layout.
- Browser admin/pengguna: 52 pemeriksaan lulus, termasuk tambah user tanpa tim, pilih/hapus tim, detail user, ubah password, cari user, tambah objek, tambah/edit tim dan perbaikan error.
- URL CSS tambahan sengaja dibuat 404 dan submit handler lama yang mencegat form disimulasikan. Halaman tetap ter-styling dan penyimpanan berjalan.
- 12 halaman diperiksa pada desktop 1440px dan mobile 390px. Tidak ditemukan overflow body atau ID duplikat pada 24 pemeriksaan DOM. Dark theme diperiksa dengan token tema aplikasi.
- Screenshot laporan/form dan halaman admin/pengguna diperiksa; kompilasi seluruh Blade dan lint PHP lulus.
- Lingkungan uji: Laravel 12, PHP 8.5, SQLite lokal, Chromium, data sintetis. Bukan pengujian pada server/MariaDB produksi pengguna. Tidak memakai atau menyertakan akun/password operasional.

Rincian cek browser dan hasil PHPUnit disertakan dalam docs/monitoring-ti/UI-FIX-V2-TESTS.md. Lakukan smoke test dengan akun PIC/ketua/anggota yang nyata setelah deploy. Tidak ada jaminan mutlak seluruh server/aplikasi bebas error hanya dari pengujian lokal.
