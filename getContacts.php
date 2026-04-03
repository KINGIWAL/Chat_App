<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    echo "Anda belum login.";
    exit;
}

$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_user = $_SESSION['id_user']; // user yang login

$sql = "SELECT id_kontak, nama, nomor FROM kontak WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// Tampilkan kontak dalam bentuk div
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='contact' data-id='" . htmlspecialchars($row['nomor']) . "'>"
             . htmlspecialchars($row['nama']) . "</div>";
    }
} else {
    echo "<p>Tidak ada kontak.</p>";
}

$stmt->close();
$conn->close();
?>
