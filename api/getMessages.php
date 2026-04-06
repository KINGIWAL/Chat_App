<?php
require __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    echo "<p>Anda belum login.</p>";
    exit;
}

$id_user     = (int)$_SESSION['id_user']; 
$id_Penerima = (int)$_POST['id_Penerima']; 

$sql = "SELECT id_Pengirim, id_Penerima, Pesan, time_Pengiriman
        FROM pesan
        WHERE (id_Pengirim = ? AND id_Penerima = ?)
           OR (id_Pengirim = ? AND id_Penerima = ?)
        ORDER BY time_Pengiriman ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user, $id_Penerima, $id_Penerima, $id_user]);

$result = $stmt->fetchAll();

if (count($result) > 0) {
    foreach ($result as $row) {
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
