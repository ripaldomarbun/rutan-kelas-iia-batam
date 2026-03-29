<?php
// ============================================================
// FILE: backend/config/database.php
// Koneksi ke database menggunakan PDO
// ============================================================

require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST
             . ";dbname="    . DB_NAME
             . ";charset="   . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Di production, jangan tampilkan pesan error ke user
            http_response_code(500);
            die(json_encode([
                'status'  => 'error',
                'message' => 'Koneksi database gagal. Hubungi administrator.'
            ]));
        }
    }

    return $pdo;
}
