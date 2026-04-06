<?php
require __DIR__ . '/db.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    echo "Anda belum login.";
    exit;
}

$id_user = $_SESSION['id_user']; // user yang login

// Query untuk mengambil kontak
$sql = "SELECT id_kontak, id_penerima, nama, nomor 
        FROM kontak 
        WHERE id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);

// Ambil semua hasil
$result = $stmt->fetchAll();

if (count($result) > 0) {
    foreach ($result as $row) {
        echo "<div class='contact' data-id='" . htmlspecialchars($row['id_penerima']) . "'>"
           . htmlspecialchars($row['nama']) . "</div>";
    }
} else {
    echo "<p>Tidak ada kontak.</p>";
}
