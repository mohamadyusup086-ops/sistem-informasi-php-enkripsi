<?php
// Konfigurasi database dan helper umum
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'sistem_enkripsi');
define('DB_USER', 'root');
define('DB_PASS', '');

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
    return $pdo;
}

// Halaman admin hanya bisa diakses setelah kedua tahap login berhasil
function requireLogin(): void
{
    if (empty($_SESSION['admin_login'])) {
        header('Location: login.php');
        exit;
    }
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
