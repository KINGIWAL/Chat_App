<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    echo "<p>Anda belum login.</p>";
    exit;
}

$id_user = (int)$_SESSION['id_user']; // mengambil id user dari session 
$id_Penerima = (int)$_POST['id_Penerima']; // SEKARANG id_user

$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query pesan dua arah (SIMPLE)
$sql = "SELECT id_Pengirim,id_Penerima, Pesan, time_Pengiriman
        FROM pesan
        WHERE (id_Pengirim = ? AND id_Penerima = ?)
           OR (id_Pengirim = ? AND id_Penerima = ?)
        ORDER BY time_Pengiriman ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $id_user, $id_Penerima, $id_Penerima, $id_user);//penetapan variabelnya 
$stmt->execute();
$result = $stmt->get_result();

// Tampilkan pesan
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pesan = htmlspecialchars($row['Pesan']);
        $waktu = htmlspecialchars($row['time_Pengiriman']);

        if ((int)$row['id_Pengirim'] === $id_user) {
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