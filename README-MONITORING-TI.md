# Timcare — Patch Monitoring Perangkat & Aplikasi TI
Versi paket: 2026-10-19. Patch untuk source `timcare-dev.zip` yang dikirim pengguna.

## Penting: bukan hanya ekstrak
Ekstrak menempatkan kode, tetapi tabel baru harus dibuat satu kali. Setelah backup dan ekstrak, jalankan skrip pemasangan di bawah. Tidak perlu composer install/update atau npm install/build. Patch tidak membawa .env, vendor, node_modules, database lama, storage/upload, atau dump SQL.

Skema sudah dicocokkan secara statis dengan dump MariaDB 10.11.19 yang diberikan: tabel users sudah memiliki soft delete, ID unsigned bigint, dan tabel logs sudah memiliki kolom audit. Belum dipasang atau diuji terhadap server produksi. Pengujian fungsional menggunakan Laravel 12, PHP 8.5, SQLite lokal; hasil dan batasan ada di docs/monitoring-ti/TEST-REPORT.md. Gunakan staging terlebih dahulu jika tersedia.

## 1. Sebelum menimpa
1. Backup database dan file aplikasi yang sedang berjalan. Simpan backup di luar direktori web.
2. Pastikan source server masih sesuai dengan source ZIP awal. File lama yang ditimpa tercantum di docs/monitoring-ti/FILES.json. Jika telah dimodifikasi lagi, gabungkan perubahan terlebih dahulu; jangan menimpa pekerjaan baru secara buta.
3. Melalui Terminal/SSH/cPanel Terminal, masuk ke root Laravel (folder berisi artisan, app, routes, .env, dan vendor), lalu aktifkan maintenance SEBELUM mengekstrak:

```bash
php artisan down --retry=60
```

4. Ekstrak isi ZIP langsung ke root Laravel dan izinkan overwrite. Jangan membuat satu folder tambahan di atas app/routes/resources/public. Bila document root hosting terpisah, tempatkan public/css/timcare-ti.css ke direktori public yang benar sesuai deployment Anda.

## 2. Pemasangan satu perintah
Dari root Laravel:

```bash
bash install-monitoring-ti.sh --backup-confirmed
```

Jika PHP CLI hosting bukan versi aplikasi, contoh cPanel:

```bash
PHP_BIN=/opt/cpanel/ea-php84/root/usr/bin/php bash install-monitoring-ti.sh --backup-confirmed
```

Skrip hanya menjalankan migrasi BARU berikut, mengimpor referensi secara additive, membersihkan cache Laravel, mengompilasi view, lalu membuka aplikasi kembali:

```bash
php artisan migrate --path=database/migrations/2026_10_19_000001_create_timcare_ti_monitoring_tables.php --force
php artisan timcare:import-ti-reference --apply
php artisan optimize:clear
php artisan view:cache
php artisan up
```

Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, atau mengimpor ulang dump SQL. Jangan mengganti langkah migrasi spesifik ini dengan `php artisan migrate` tanpa --path: dump yang diberikan mempunyai tabel lama yang tidak semuanya tercatat dalam daftar migrations.

Jika skrip gagal, aplikasi sengaja tetap dalam maintenance. Periksa error/log dan perbaiki penyebab sebelum mengaktifkan aplikasi. Tidak ada skrip installer HTTP publik yang membuka akses migrasi melalui browser. Jika tidak memiliki Terminal/SSH, minta hosting menjalankan perintah tersebut; mengekstrak file saja belum cukup.

## 3. Penyiapan setelah pemasangan
Login Admin, lalu:
1. Monitoring TI → Tim kerja: periksa ketua tiap tim. Admin bisa mengganti ketua atau mengosongkannya. Ketua yang dipilih otomatis menjadi anggota tim.
2. Monitoring TI → Tim kerja: pada baris tim yang dituju, pilih Atur anggota. Simpan perubahan hanya untuk tim tersebut; pegawai dapat menjadi anggota beberapa tim. Manajemen User hanya mengelola akun dan menampilkan keanggotaan saat ini.
3. Monitoring TI → Objek & penanggung jawab → Atur objek: pilih PIC dan satu/lebih tim pengelola. Ini WAJIB untuk memberi hak pengisian. Tidak ada pemetaan otomatis berdasarkan kemiripan nama.
4. PIC/ketua tim membuka Monitoring TI, memilih aplikasi, lalu langsung memilih salah satu jenis laporan: Pemanfaatan, Monitoring, atau Tindak lanjut. Tahun dan bulan/semester sudah diarahkan ke periode berjalan dan tetap dapat disesuaikan sebelum laporan disimpan.
5. Riwayat laporan dapat dicari berdasarkan nama/kode aplikasi dan difilter menurut aplikasi, jenis, tahun, atau periode. Unduhan CSV mengikuti filter aktif dan dapat dibuka di Excel; bukan ekspor workbook XLSX persis seperti template.

Contoh: untuk SIMPEG BPS, pilih tim SDM yang benar dan akun PIC yang sebenarnya. Setelah itu hanya PIC dan ketua tim yang dipilih yang dapat mengubah laporan SIMPEG. Ketua tim lain, anggota biasa, Teknisi, atau Admin yang tidak ditunjuk tidak mendapat hak ubah laporan tersebut.

## 4. Apa yang diimpor
- 18 nama tim dari pegawai-tim.json.
- Keanggotaan akun yang SUDAH ADA, dicocokkan berdasarkan email setelah trim dan lowercase. Tidak mencocokkan berdasarkan nama/NIP/nomor urut dan tidak membuat akun baru.
- Ketua diambil hanya dari isKetua=true yang eksplisit, dengan satu kandidat akun cocok. Bila belum pasti/akun belum ada, dibiarkan kosong dan dilaporkan. Ada entri keanggotaan sumber tanpa flag isKetua; flag yang hilang tidak dianggap ketua.
- 43 master objek dari sheet Daftar Objek TI (termasuk nama berlabel [DRAF] sesuai sumber).
- Tiga kelompok cakupan: Aplikasi & Layanan, Infrastruktur, Keamanan. Skala penilaian diambil dari sheet Referensi Skala.

Password, role/capability flags SIAP, username, NIP, dan atribut pribadi lain dari JSON TIDAK digunakan. File referensi dalam patch hanya memuat nama tim, email pencocokan, keanggotaan/flag ketua, objek, dan skala yang diperlukan. Tidak ada password sumber.

Teks PJ/Pengelola dari Excel tetap disimpan sebagai referensi. PIC Excel yang kosong tidak ditebak. Relasi tim pengelola objek juga tidak ditebak karena label sumber tidak identik dengan daftar tim. Tidak ada contoh laporan Excel yang dimasukkan sebagai data operasional.

Import tidak menimpa nama objek yang sudah ada, PIC/penugasan tim, ketua yang sudah terisi, role, password, atau data akun. Keanggotaan ditambah tanpa menghapus yang lama. Boleh dijalankan kembali setelah akun yang belum cocok tersedia:

```bash
# Pemeriksaan saja, tidak mengimpor tim/objek/keanggotaan:
php artisan timcare:import-ti-reference
# Simpan referensi yang belum ada:
php artisan timcare:import-ti-reference --apply
```

Jika ingin menghapus keanggotaan atau mengganti ketua yang sudah ditetapkan, lakukan melalui UI; impor additive tidak berfungsi sebagai sinkronisasi penghapusan. Pegawai yang tidak mempunyai tim dalam JSON tidak otomatis kehilangan tim yang sudah ada di server.

## 5. Aturan akses
| Pengguna | Baca laporan | Buat/edit laporan | Kelola objek/tim/PIC |
|---|---|---|---|
| PIC objek (termasuk tanpa tim) | Objek yang ditangani | Ya, objek aktif yang ditangani | Hanya jika juga Admin |
| Ketua tim pengelola objek | Objek timnya | Ya, objek aktif timnya | Hanya jika juga Admin |
| Anggota tim biasa | Objek timnya | Tidak | Tidak |
| Admin | Semua | Tidak otomatis; harus PIC/ketua terkait | Ya |
| Teknisi/ULP/pegawai lain | Hanya jika PIC/anggota/ketua terkait | Hanya jika PIC/ketua terkait | Tidak |

Ini menerapkan pembacaan minimum sesuai relasi PIC/tim; role pimpinan SIAP dari JSON tidak diimpor menjadi role Timcare. Jika nanti dibutuhkan pimpinan non-Admin yang dapat membaca seluruh laporan, tambahkan kebutuhan tersebut secara eksplisit, bukan dengan mengimpor flag JSON.

Pembatasan berlaku di server pada URL langsung/POST/PUT/ekspor, bukan sekadar menyembunyikan tombol. Hak berubah mengikuti PIC/ketua/tim pengelola saat ini, bukan pembuat laporan lama. Objek nonaktif tetap dapat dibaca, tetapi tidak dapat diisi/diedit. Mengganti ketua mempertahankan mantan ketua sebagai anggota sampai Admin mengubah keanggotaannya.

## 6. Isi formulir
### Pemanfaatan TI (bulanan)
Objek, bulan, tahun, jumlah pengguna, jumlah pemanfaatan, update fitur Ya/Tidak, keterangan update. Keterangan wajib bila update fitur Ya.

### Monitoring & Evaluasi TI (bulanan)
Objek, bulan, tahun, cakupan/aspek otomatis, kondisi/capaian operasional, tingkat pemanfaatan, permasalahan/kendala, tingkat kendala, tindakan koreksi/solusi, efektivitas tindakan, status Open/Closed/Carry-over, catatan. Efektivitas tindakan boleh kosong bila tidak ada tindakan yang perlu dinilai. Cakupan/aspek disimpan sebagai snapshot agar riwayat tidak berubah ketika master diubah.

### Tindak Lanjut Monev (semesteran)
Objek, semester I/II, tahun, ringkasan kendala, detail tindak lanjut, link bukti HTTP/HTTPS, penanggung jawab tindak lanjut, target selesai, status Belum/Proses/Selesai, catatan hasil rapat. Link bukti merupakan tautan, bukan unggahan file baru. Tidak otomatis membuat tiket Timcare atau menyalin kendala semester sebelumnya.

Satu laporan per objek + jenis + tahun + periode. Form monitoring dan pemanfaatan dapat berdampingan pada bulan yang sama. Laporan duplikat ditolak di validasi dan unique index database. Identitas/jenis/periode tidak dipindahkan saat edit; kolom isian tetap dapat diperbaiki oleh petugas berhak. Tidak ada fitur hapus laporan permanen dalam patch ini untuk mempertahankan audit trail. Kesalahan periode yang sudah tersimpan perlu penanganan administratif terkontrol; jangan membuat duplikat tanpa meninjau data.

## 7. Perubahan database
Tidak menambah team_id tunggal ke users karena satu pegawai dapat memiliki banyak tim. Tim kosong berarti tidak ada baris pada tabel relasi, bukan nilai wajib.
Tabel baru: ti_teams, ti_team_user, ti_objects, ti_object_team, ti_reports. Data tiket/aset/reservasi/piket dan kredensial existing tidak ditimpa. Entri aktivitas baru dicatat di tabel logs existing. Model User mendapatkan relasi teams() dan ledTeams().

Tidak ada integrasi impor Excel dinamis di UI, notifikasi monitoring otomatis, reminder, persetujuan berjenjang, auto-escalation, atau perubahan pada alur tiket lama. Semua itu di luar patch ini.

## 8. Pemeriksaan setelah pasang
- Admin: menu Monitoring TI terlihat; 18 tim dan 43 objek terisi sesudah impor pertama pada tabel baru.
- User tanpa tim tetap bisa disimpan dan login. Beberapa tim bisa dipilih pada satu user.
- Pastikan PIC dan ketua tim benar sebelum membuka pengisian laporan.
- PIC tanpa tim dapat menyimpan laporan objeknya; ketua tim terkait dapat mengeditnya.
- Anggota biasa hanya baca; ketua tim lain tidak dapat mengubah laporan.
- Uji dengan URL langsung dan bukan hanya tombol di layar.
- Isi formulir valid, coba periode ganda, coba dua browser mengedit laporan bersamaan.
- Pastikan filter/CSV hanya berisi objek yang boleh dilihat.
- Uji modul lama: login/SSO, tiket, Zoom, aset, notifikasi, manajemen user. Modul tersebut tidak diregresi secara end-to-end di lingkungan server Anda.
- Jika opcode cache hosting masih menampilkan kode lama, gunakan prosedur reload PHP/OPcache yang disediakan hosting, lalu refresh browser.

## 9. Pembatalan pemasangan
Aktifkan maintenance. Pulihkan file lama yang ditimpa dari backup milik Anda. Tabel baru boleh tetap disimpan agar laporan baru tidak hilang. Jangan menjalankan rollback/drop tabel tanpa backup data monitoring. Jika perlu memulihkan seluruh database, pertimbangkan transaksi baru sejak backup agar tidak hilang. Jangan mengimpor dump lama langsung ke database aktif.

## 10. Berkas paket
Lihat docs/monitoring-ti/FILES.json dan CHECKSUMS.sha256 untuk daftar file, hash source awal (file existing), serta hash patch. Paket tidak mengandung runtime/vendor lingkungan pengujian lokal.
