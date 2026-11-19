<?php
require 'config_db.php';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sistem Informasi Terenkripsi</title>
<style>
body{font-family:system-ui,sans-serif;margin:0;background:#0f172a;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center}
.wrap{max-width:640px;padding:32px;text-align:center}
h1{font-size:2.2rem;margin-bottom:12px}
p{line-height:1.6;color:#cbd5e1}
a.btn{display:inline-block;margin-top:24px;padding:12px 28px;background:#3b82f6;color:#fff;text-decoration:none;border-radius:8px;font-weight:600}
</style>
</head>
<body>
<div class="wrap">
  <h1>Sistem Informasi Terenkripsi</h1>
  <p>Seluruh data yang tersimpan dienkripsi dengan AES-256-GCM. Kunci AES dilindungi oleh RSA 2048-bit. Area admin hanya dapat diakses dengan kunci simetris dan kunci asimetris.</p>
  <a class="btn" href="login.php">Masuk ke Admin</a>
</div>
</body>
</html>
