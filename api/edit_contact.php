<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)$_POST['id'];
    $nama = $_POST['nama'];
    $nomor = $_POST['nomor'];


    $stmt = $pdo->prepare("UPDATE kontak SET nama = ?,nomor = ? WHERE id_penerima = ?");
    $stmt->execute([$nama,$nomor, $id]);

    echo "Kontak berhasil diupdate";
} else {
    echo "Metode tidak valid";
}
