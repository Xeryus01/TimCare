# Hasil validasi patch Monitoring TI

## Hasil
- PHPUnit: 28 test, 110 assertion, seluruhnya lulus.
- Pemeriksaan akhir terhadap 133 file PHP di app, routes, dan database/migrations lolos php -l.
- Seluruh template Blade dikompilasi dengan `view:cache` tanpa error.
- 15 rute monitoring terdaftar.
- Tujuh halaman diperiksa pada lebar 1440px dan 390px: daftar laporan, objek, formulir monitoring, pengaturan objek, tim, edit user, detail laporan.
- 14 pemeriksaan DOM tidak menemukan ID duplikat atau overflow horizontal pada body. Tabel lebar memakai area scroll tersendiri.
- Screenshot formulir desktop/mobile, daftar objek desktop, dan edit user mobile ditinjau. Sisa directive Blade yang ditemukan pada pemeriksaan awal telah diperbaiki dan ditambahkan assertion regresinya.

## Lingkungan & batasan
Laravel 12 / PHP 8.5.9 / SQLite terisolasi, dependensi QA terpisah. Tidak menggunakan password atau salinan data operasional pengguna. Skema dump MariaDB 10.11.19 diperiksa secara statis, tetapi migrasi belum dieksekusi pada MariaDB produksi. Tidak mengklaim pengujian seluruh aplikasi, SSO/email/WhatsApp/queue, atau server hosting pengguna.

Untuk preview, Blade dirender melalui aplikasi uji dengan data sintetis, menggunakan CSS/build lama dan CSS patch. Alpine CDN existing disimulasikan dengan salinan lokal untuk pemeriksaan visual; file tersebut tidak masuk patch. Preview ini bukan bukti koneksi ke server pengguna.

## Cakupan pengujian otomatis
1. User tanpa tim atau beberapa tim.
2. PIC dan ketua tim terkait saja yang boleh edit.
3. Ketua tim lain tidak berhak.
4. PIC tanpa tim dapat mengisi ketiga jenis laporan.
5. Anggota biasa ditolak ketika melakukan POST langsung.
6. Admin tidak memiliki bypass edit laporan.
7. Pengguna belum login diarahkan ke login.
8. Pegawai lain ditolak pada akses laporan langsung.
9. Duplikasi periode ditolak validasi.
10. Unique constraint database menolak duplikasi.
11. Skala tidak valid, angka negatif, dan semester/bulan tidak valid ditolak.
12. Update fitur Ya wajib berketerangan.
13. URL javascript pada bukti ditolak.
14. Update menjaga identitas laporan, membuang field tak diizinkan, dan mencatat audit.
15. Konflik versi edit ditolak agar tidak saling menimpa.
16. Pergantian PIC langsung mencabut hak PIC lama.
17. Objek nonaktif baca-saja.
18. Ekspor CSV mengikuti cakupan akses dan menetralkan formula injection.
19. Non-Admin ditolak mengubah master/tim.
20. Ketua otomatis menjadi anggota tim.
21. Tim pegawai dapat dikosongkan (termasuk input null), password tetap.
22. Keanggotaan ketua tidak terhapus tanpa mengganti ketua dahulu.
23. Request klien lama tanpa field pemilihan tim tidak menghapus keanggotaan.
24. PIC soft-deleted tidak dapat ditugaskan.
25. Impor dry-run tidak mengimpor data; apply additive/idempotent; akun/role/password/penugasan existing tidak ditimpa.
26. Snapshot cakupan/aspek bertahan saat master berubah.
27. Semua halaman baru dan halaman user terkait dapat dirender.
28. Fixture visual tujuh halaman menghasilkan HTML tanpa directive Blade tertinggal.
