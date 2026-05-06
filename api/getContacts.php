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
$sql = "SELECT c.id_kontak, c.id_penerima, c.nama, c.nomor, u.foto_profile
        FROM kontak c
        JOIN user u ON c.id_penerima = u.id_user
        WHERE c.id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);

// Ambil semua hasil
$result = $stmt->fetchAll();

if (count($result) > 0) {
    foreach ($result as $row) {
        $foto = !empty($row['foto_profile']) ? htmlspecialchars($row['foto_profile']) : 'uploads/default.jpg';
        echo "
        <div class='contact' data-id='" . htmlspecialchars($row['id_penerima']) . "' 
     data-nomor='" . htmlspecialchars($row['nomor']) . "'>
    <img src='" . $foto . "' alt='Foto' class='contact-photo' width='40' height='40'>
    <span class='contact-name'>" . htmlspecialchars($row['nama']) . "</span>
    <div class='contact-options'>
        <button class='options-btn'>⋮</button>
        <div class='options-dropdown'>
            <a href='#' class='delete-contact' data-id='" . htmlspecialchars($row['id_penerima']) . "'>Hapus</a>
            <a href='#' class='edit-contact' data-id='" . htmlspecialchars($row['id_penerima']) . "'>Edit</a>
        </div>
    </div>
</div>";
        
    }
} else {
    echo "<p>Tidak ada kontak.</p>";
}


