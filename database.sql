CREATE DATABASE IF NOT EXISTS sistem_enkripsi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistem_enkripsi;

CREATE TABLE kunci_admin (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    passphrase_hash VARCHAR(255) NOT NULL,
    public_key      TEXT NOT NULL
);

CREATE TABLE konten (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    iv         VARCHAR(64)   NOT NULL,
    tag        VARCHAR(64)   NOT NULL,
    cipher     MEDIUMTEXT    NOT NULL,
    kunci      TEXT          NOT NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);
