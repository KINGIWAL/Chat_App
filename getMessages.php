<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    echo "<p>Anda belum login.</p>";
    exit;
}

$currentUser = (int)$_SESSION['id_user'];
$id_Penerima = (string)$_POST['id_Penerima']; // BIGINT → simpan sebagai string

$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil nomor_user dari user login
$sql = "SELECT nomor_user FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$nomor_user_login = $row['nomor_user'];

// Ambil id_user dari lawan bicara berdasarkan nomor_user (BIGINT)
$sql = "SELECT id_user FROM user WHERE nomor_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_Penerima);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$id_user_penerima = (int)$row['id_user'];

// Query pesan dua arah dengan mapping
$sql = "SELECT id_Pengirim, Pesan, time_Pengiriman
        FROM pesan
        WHERE (id_Pengirim = ? AND id_Penerima = ?)
           OR (id_Pengirim = ? AND id_Penerima = ?)
        ORDER BY time_Pengiriman ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isis", $currentUser, $id_Penerima, $id_user_penerima, $nomor_user_login);
$stmt->execute();
$result = $stmt->get_result();

// Tampilkan pesan
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pesan = htmlspecialchars($row['Pesan']);
        $waktu = htmlspecialchars($row['time_Pengiriman']);

        if ((int)$row['id_Pengirim'] === $currentUser) {
            echo "<div class='message sent'>
                    <div class='bubble'>{$pesan}</div>
                    <div class='time'>{$waktu}</div>
                  </div>";
        } else {
            echo "<div class='message received'>
                    <div class='bubble'>{$pesan}</div>
                    <div class='time'>{$waktu}</div>
                  </div>";
        }
    }
} else {
    echo "<p>Tidak ada Pesan.</p>";
}

$stmt->close();
$conn->close();
?>
