<?php
require 'config_db.php';
require 'crypto.php';

if (isset($_GET['reset'])) {
    unset($_SESSION['login_step'], $_SESSION['challenge'], $_SESSION['challenge_cipher']);
    header('Location: login.php');
    exit;
}

$step  = $_SESSION['login_step'] ?? 1;
$error = '';
$row   = db()->query('SELECT passphrase_hash, public_key FROM kunci_admin LIMIT 1')->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $row) {

    // Tahap 1: kunci simetris (passphrase)
    if (isset($_POST['passphrase'])) {
        if (password_verify($_POST['passphrase'], $row['passphrase_hash'])) {
            session_regenerate_id(true);

            // Buat tantangan acak, lalu enkripsi dengan kunci publik
            $challenge = bin2hex(random_bytes(16));
            openssl_public_encrypt($challenge, $encrypted, $row['public_key'], OPENSSL_PKCS1_OAEP_PADDING);

            $_SESSION['login_step']       = 2;
            $_SESSION['challenge']        = $challenge;
            $_SESSION['challenge_cipher'] = base64_encode($encrypted);
        } else {
            $error = 'Kunci simetris salah.';
        }
        if (!$error) {
            header('Location: login.php');
            exit;
        }
    }

    // Tahap 2: kunci asimetris (unggah private.pem)
    elseif (isset($_FILES['private_key'])) {
        if ($step !== 2 || empty($_SESSION['challenge_cipher'])) {
            $error = 'Selesaikan tahap 1 terlebih dahulu.';
        } elseif ($_FILES['private_key']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Gagal mengunggah file kunci.';
        } else {
            $pem     = file_get_contents($_FILES['private_key']['tmp_name']);
            $priv    = @openssl_pkey_get_private($pem);
            $decoded = '';
            $cocok   = $priv
                && openssl_private_decrypt(
                    base64_decode($_SESSION['challenge_cipher']),
                    $decoded,
                    $priv,
                    OPENSSL_PKCS1_OAEP_PADDING
                )
                && hash_equals($_SESSION['challenge'], $decoded);

            if ($cocok) {
                $_SESSION['admin_login'] = true;
                unset($_SESSION['login_step'], $_SESSION['challenge'], $_SESSION['challenge_cipher']);
                header('Location: admin_dashboard.php');
                exit;
            }
            $error = 'Kunci asimetris tidak cocok.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin</title>
<style>
body{font-family:system-ui,sans-serif;background:#f3f4f6;display:flex;justify-content:center;padding:40px}
.card{background:#fff;padding:28px;border-radius:12px;max-width:420px;width:100%;box-shadow:0 4px 16px rgba(0,0,0,.08)}
input,button{width:100%;padding:10px;margin-top:8px;box-sizing:border-box}
button{background:#1d4ed8;color:#fff;border:0;border-radius:6px;cursor:pointer}
.err{color:#b91c1c}
.box{background:#f9fafb;border:1px solid #e5e7eb;padding:10px;word-break:break-all;font-size:12px;border-radius:6px}
.step{color:#6b7280;font-size:14px}
</style>
</head>
<body>
<div class="card">
<?php if (!$row): ?>
  <h2>Belum disetup</h2>
  <p>Jalankan <b>setup.php</b> terlebih dahulu.</p>

<?php elseif ($step === 1): ?>
  <h2>Login Admin</h2>
  <p class="step">Tahap 1 dari 2: masukkan kunci simetris.</p>
  <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <input type="password" name="passphrase" placeholder="Kunci simetris" required>
    <button type="submit">Lanjut</button>
  </form>

<?php else: ?>
  <h2>Login Admin</h2>
  <p class="step">Tahap 2 dari 2: unggah kunci privat (private.pem) untuk membuka tantangan berikut.</p>
  <p>Tantangan terenkripsi (dienkripsi dengan kunci publik):</p>
  <div class="box"><?= e($_SESSION['challenge_cipher']) ?></div>
  <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="private_key" accept=".pem" required>
    <button type="submit">Verifikasi</button>
  </form>
  <p><a href="login.php?reset=1">Mulai ulang</a></p>
<?php endif; ?>
</div>
</body>
</html>
