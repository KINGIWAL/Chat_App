<?php
session_start();
$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_Pengirim = $_SESSION['id_user'];
$id_Penerima = $_POST['id_Penerima'];
$Pesan = $_POST['Pesan'];

$sql = "INSERT INTO pesan (id_Pengirim, id_Penerima, Pesan) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $id_Pengirim, $id_Penerima, $Pesan);

if ($stmt->execute()) {
    echo htmlspecialchars($Pesan); // balas pesan yang baru dikirim
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>