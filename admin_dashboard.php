<?php
require 'config_db.php';
requireLogin();

$rows = db()->query('SELECT id, iv, tag, cipher, kunci, created_at FROM konten ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin</title>
<style>
body{font-family:system-ui,sans-serif;margin:0;background:#f3f4f6;padding:24px}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
a.btn{padding:8px 16px;background:#1d4ed8;color:#fff;text-decoration:none;border-radius:6px}
a.out{color:#b91c1c}
.item{background:#fff;padding:16px;border-radius:10px;margin-bottom:14px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.label{font-size:12px;color:#6b7280;margin-top:8px;font-weight:600}
.val{font-family:monospace;font-size:12px;word-break:break-all;background:#f9fafb;padding:6px;border-radius:4px}
</style>
</head>
<body>
<div class="top">
  <h2>Data Terenkripsi</h2>
  <div>
    <a class="btn" href="admin_tambah.php">+ Tambah Data</a>
    &nbsp; <a class="out" href="admin_logout.php">Keluar</a>
  </div>
</div>

<?php if (!$rows): ?>
  <p>Belum ada data.</p>
<?php endif; ?>

<?php foreach ($rows as $r): ?>
  <div class="item">
    <strong>ID #<?= (int)$r['id'] ?></strong> &middot; <?= e($r['created_at']) ?>
    <div class="label">IV</div><div class="val"><?= e($r['iv']) ?></div>
    <div class="label">Tag</div><div class="val"><?= e($r['tag']) ?></div>
    <div class="label">Ciphertext</div><div class="val"><?= e($r['cipher']) ?></div>
    <div class="label">Kunci AES terbungkus (RSA)</div><div class="val"><?= e($r['kunci']) ?></div>
  </div>
<?php endforeach; ?>
</body>
</html>
