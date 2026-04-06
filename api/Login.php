<?php
require __DIR__ . '/db.php'; // koneksi
session_start();

// Ambil data dari form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Query untuk ambil data user berdasarkan username
$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);

// Ambil satu baris hasil
$row = $stmt->fetch();

if ($row) {
    // Verifikasi password dengan hash di database
    if (password_verify($password, $row['password'])) {
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['username'] = $row['username'];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Password salah!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
}
