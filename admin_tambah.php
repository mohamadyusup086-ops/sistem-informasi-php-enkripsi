<?php
require 'config_db.php';
require 'crypto.php';
requireLogin();

$error = '';
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $isi   = trim($_POST['isi'] ?? '');

    if ($judul === '' || $isi === '') {
        $error = 'Judul dan isi wajib diisi.';
    } else {
        try {
            $publicKey = db()->query('SELECT public_key FROM kunci_admin LIMIT 1')->fetchColumn();
            $payload   = json_encode(['judul' => $judul, 'isi' => $isi], JSON_UNESCAPED_UNICODE);
            $enc       = encryptRecord($payload, $publicKey);

            db()->prepare('INSERT INTO konten (iv, tag, cipher, kunci) VALUES (?, ?, ?, ?)')
                ->execute([$enc['iv'], $enc['tag'], $enc['cipher'], $enc['kunci']]);

            header('Location: admin_dashboard.php');
            exit;
        } catch (Throwable $ex) {
            $error = 'Gagal menyimpan: ' . $ex->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tambah Data</title>
<style>
body{font-family:system-ui,sans-serif;background:#f3f4f6;padding:24px}
.card{background:#fff;padding:24px;border-radius:10px;max-width:640px;margin:auto}
input,textarea,button{width:100%;padding:10px;margin-top:6px;margin-bottom:14px;box-sizing:border-box}
button{background:#1d4ed8;color:#fff;border:0;border-radius:6px;cursor:pointer}
.err{color:#b91c1c}
</style>
</head>
<body>
<div class="card">
  <h2>Tambah Data</h2>
  <p>Data akan dienkripsi sebelum disimpan ke database.</p>
  <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Judul</label>
    <input type="text" name="judul" required>
    <label>Isi</label>
    <textarea name="isi" rows="6" required></textarea>
    <button type="submit">Simpan Terenkripsi</button>
  </form>
  <a href="admin_dashboard.php">&larr; Kembali</a>
</div>
</body>
</html>
