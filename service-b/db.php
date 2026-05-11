<?php
// Konfigurasi koneksi database MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'mahasiswa');  
define('DB_PASS', 'akucintafik');  
define('DB_NAME', '2410511081_db_hidroponik');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}