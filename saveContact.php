<?php
session_start();
$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_user = $_SESSION['id_user']; // user yang login
$nama = $_POST['nama'];
$nomor = $_POST['nomor'];

$sql = "INSERT INTO kontak (id_user, nama, nomor) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $id_user, $nama, $nomor);

if ($stmt->execute()) {
    echo "Kontak berhasil disimpan!";
} else {
    echo "Gagal menyimpan kontak.";
}

$stmt->close();
$conn->close();
?>