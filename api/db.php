<?php
// Konfigurasi database
$host     = 'localhost';
$dbname   = 'chat_app';
$username = 'root';
$password = 'muhammadilham2372005';

// Opsi tambahan untuk keamanan
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // error jadi exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // hasil fetch jadi array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // gunakan prepared statement asli
];

try {
    // Buat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    // Jangan tampilkan detail error ke user, cukup log internal
    error_log("Database connection failed: " . $e->getMessage());
    die("Koneksi database gagal."); // pesan umum
}
