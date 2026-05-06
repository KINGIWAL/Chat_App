<?php
require __DIR__ . '/db.php';

if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM kontak WHERE id_penerima = ?");
    $stmt->execute([$id]);
    echo "Kontak berhasil dihapus";
} else {
    echo "ID tidak valid";
}
