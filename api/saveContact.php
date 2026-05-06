<?php
require __DIR__ . '/db.php';
session_start();

$id_user = $_SESSION['id_user'] ?? null;
$nama    = $_POST['nama'] ?? '';
$nomor   = $_POST['nomor'] ?? '';

if (!$id_user) {
    echo "Anda belum login.";
    exit;
}
// VALIDASI 
// Ambil id_user dari nomor
$stmt = $pdo->prepare("SELECT id_user FROM user WHERE nomor_user = ?");
$stmt->execute([$nomor]);
$row = $stmt->fetch();

$id_penerima = $row ? (int)$row['id_user'] : null;

// Validasi
if (!$id_penerima) {
    echo "Nomor tidak ditemukan!";
    exit;
}

// Simpan kontak
$sql = "INSERT INTO kontak (id_user, nama, nomor, id_penerima) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$id_user, $nama, $nomor, $id_penerima]);

if ($success) {
    echo "Kontak berhasil disimpan!";
} else {
    echo "Gagal menyimpan kontak.";
}
