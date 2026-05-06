<?php
require __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['error' => 'Belum login']);
    exit;
}

$id_user = (int)$_SESSION['id_user'];

$stmt = $pdo->prepare("SELECT username, nomor_user, foto_profile FROM user WHERE id_user = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User tidak ditemukan']);
}
