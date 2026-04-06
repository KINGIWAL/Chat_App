<?php
require __DIR__ . '/db.php';
session_start();

$username = $_POST['username'] ?? '';
$password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);

// Generate nomor_user random 14 digit
$nomor_user = str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);

try {
    // Insert ke database
    $sql = "INSERT INTO user (username, password, nomor_user) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$username, $password, $nomor_user]);

    if ($success) {
        echo "Registrasi berhasil! Nomor user: " . htmlspecialchars($nomor_user);
    } else {
        echo "Registrasi gagal.";
    }
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo "Terjadi kesalahan saat registrasi.";
}
