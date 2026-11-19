<?php
// Pola envelope: data dienkripsi AES-256-GCM dengan kunci acak per data,
// lalu kunci AES itu dienkripsi dengan RSA (kunci publik) dan disimpan bersama data.

function encryptRecord(string $plain, string $publicKeyPem): array
{
    $aesKey = random_bytes(32);
    $iv     = random_bytes(12);
    $tag    = '';

    $cipher = openssl_encrypt($plain, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
    if ($cipher === false) {
        throw new RuntimeException('Enkripsi AES gagal.');
    }

    if (!openssl_public_encrypt($aesKey, $wrappedKey, $publicKeyPem, OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new RuntimeException('Pembungkusan kunci RSA gagal.');
    }

    return [
        'iv'     => base64_encode($iv),
        'tag'    => base64_encode($tag),
        'cipher' => base64_encode($cipher),
        'kunci'  => base64_encode($wrappedKey),
    ];
}
