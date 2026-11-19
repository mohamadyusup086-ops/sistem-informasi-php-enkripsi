# Sistem Informasi Berbasis PHP dengan Enkripsi Simetris dan Asimetris

## Teknologi
- PHP native (7.4+ / 8.x), ekstensi `openssl` dan `pdo_mysql`
- MySQL / MariaDB
- Enkripsi data: AES-256-GCM dengan kunci acak per data (pola envelope)
- Pembungkusan kunci AES: RSA 2048-bit, padding OAEP

## Struktur file
| File | Fungsi |
|---|---|
| `index.php` | Landing page, tombol masuk ke admin |
| `login.php` | Login dua tahap: kunci simetris lalu kunci asimetris |
| `setup.php` | Setup sekali: membuat passphrase dan pasangan RSA, lalu mengunduh `private.pem` |
| `admin_dashboard.php` | Halaman admin, menampilkan seluruh data dalam bentuk terenkripsi |
| `admin_tambah.php` | Input data, dienkripsi sebelum disimpan |
| `admin_logout.php` | Keluar dari sesi admin |
| `config_db.php` | Koneksi database dan fungsi bantu sesi |
| `crypto.php` | Fungsi enkripsi (AES-GCM + RSA) |
| `database.sql` | Skema database |

## Cara menjalankan
1. Impor `database.sql` ke MySQL.
2. Sesuaikan `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASS` di `config_db.php`.
3. Letakkan semua file di folder web server (misalnya `htdocs/sistem-enkripsi/`).
4. Buka `setup.php` di browser, masukkan kunci simetris (minimal 8 karakter), lalu simpan file `private.pem` yang diunduh.
5. Hapus `setup.php` dari server.
6. Buka `index.php`, klik **Masuk ke Admin**, lalu:
   - Tahap 1: masukkan kunci simetris.
   - Tahap 2: unggah `private.pem`.

## Alur keamanan
- **Login simetris**: passphrase disimpan dalam bentuk hash `password_hash` (bcrypt).
- **Login asimetris**: server membuat tantangan acak, mengenkripsinya dengan kunci publik, lalu hanya pemilik kunci privat yang bisa mendekripsinya dan mengembalikan nilai yang sama. Kunci privat tidak disimpan di server.
- **Penyimpanan data**: setiap data memakai kunci AES acak 256-bit. Kunci itu dienkripsi dengan kunci publik RSA. Database hanya menyimpan `iv`, `tag`, `cipher`, dan `kunci`.
- **Halaman admin**: hanya menampilkan ciphertext sesuai kebutuhan tugas.

## Batasan yang perlu diketahui
- `private.pem` tidak bisa dipulihkan. Jika hilang, Anda tidak bisa login.
- Jika passphrase lupa, setup perlu diulang dengan mengosongkan tabel `kunci_admin` dan `konten`.
- Belum ada proteksi CSRF dan rate limit, sehingga aplikasi ini ditujukan untuk tugas kuliah, bukan produksi.
- Untuk produksi, gunakan HTTPS dan simpan kredensial database di luar folder web.
