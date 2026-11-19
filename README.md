# Sistem Informasi Berbasis PHP dengan Enkripsi Simetris dan Asimetris

Tugas mata kuliah **[Nama Mata Kuliah]** kelas C.
Nama: Mohamad Yusup (NIM 2440511006)

Repository ini berisi kode program dan skema database. Login admin memakai dua lapis kunci, yaitu passphrase (kunci simetris) dan file `private.pem` (kunci asimetris RSA). Seluruh data di database disimpan dalam bentuk terenkripsi.

## Teknologi
- PHP native (7.4+ / 8.x) dengan ekstensi `openssl` dan `pdo_mysql`
- MySQL / MariaDB
- Enkripsi data: AES-256-GCM dengan kunci acak per data (pola envelope)
- Pembungkus kunci AES: RSA 2048-bit dengan padding OAEP

## Struktur file
| File | Fungsi |
|---|---|
| `index.php` | Landing page, tombol masuk ke admin |
| `login.php` | Login dua tahap: kunci simetris lalu kunci asimetris |
| `admin_dashboard.php` | Halaman admin, menampilkan data dalam bentuk terenkripsi |
| `admin_tambah.php` | Form input data, data dienkripsi sebelum disimpan |
| `admin_logout.php` | Mengakhiri sesi admin |
| `config_db.php` | Koneksi database dan fungsi bantu |
| `crypto.php` | Fungsi enkripsi AES-GCM dan pembungkusan RSA |
| `database.sql` | Skema tabel saja (database kosong) |
| `sistem_enkripsi.sql` | Skema tabel beserta 10 data contoh terenkripsi |

File `setup.php` (pembuatan kunci), `seed.php` (pengisi data contoh), dan `decrypt_test.php` (demo dekripsi) tidak disertakan karena hanya dipakai sekali untuk persiapan atau demonstrasi.

## Cara menjalankan

### 1. Siapkan server lokal
Gunakan Laragon, XAMPP, atau MAMP. Pastikan ekstensi `openssl` dan `pdo_mysql` aktif di PHP.

### 2. Letakkan folder proyek
Salin folder ini ke folder web server, misalnya `D:\laragon\www\sistem-enkripsi-mohamadyusup\`.

### 3. Impor database
Pilih salah satu:
- **Dengan data contoh:** impor `sistem_enkripsi.sql` lewat phpMyAdmin (tab **Import**).
- **Kosong:** impor `database.sql`, lalu buat data sendiri lewat aplikasi.

### 4. Sesuaikan koneksi
Ubah `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASS` di `config_db.php` sesuai pengaturan MySQL Anda.

### 5. Buat kunci admin
Kunci dibuat sekali lewat `setup.php`. Karena file itu tidak ada di repository, gunakan salinan dari arsip demo. Setelah dijalankan, halaman akan mengunduh `private.pem`.

Jika ingin membuat ulang kunci, kosongkan tabel `kunci_admin` dan `konten` terlebih dahulu.

### 6. Login
Buka `index.php`, klik **Masuk ke Admin**, lalu:
- **Tahap 1:** masukkan passphrase.
- **Tahap 2:** unggah `private.pem`.

## Alur keamanan
- **Login simetris:** passphrase disimpan sebagai hash `password_hash` (bcrypt), tidak dalam bentuk teks.
- **Login asimetris:** server membuat tantangan acak, mengenkripsinya dengan kunci publik, lalu hanya pemilik kunci privat yang bisa mendekripsinya. Kunci privat tidak disimpan di server.
- **Penyimpanan data:** setiap data memakai kunci AES acak 256-bit. Kunci itu dibungkus dengan kunci publik RSA. Database hanya menyimpan `iv`, `tag`, `cipher`, dan `kunci`.
- **Halaman admin:** hanya menampilkan data terenkripsi, tanpa teks asli.

## Catatan penting
- File `private.pem` **tidak disertakan** di repository. Simpan di tempat yang aman di luar folder proyek.
- Jika `private.pem` hilang, data tidak bisa dibuka kembali dan login tidak bisa dilakukan.
- Repository ini ditujukan untuk tugas kuliah, bukan produksi. Belum ada proteksi CSRF dan rate limit.
- Untuk penggunaan nyata, gunakan HTTPS dan simpan kredensial database di luar folder web.
