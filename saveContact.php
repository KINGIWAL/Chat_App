<?php
session_start();
$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_user = $_SESSION['id_user'];
$nama = $_POST['nama'];
$nomor = $_POST['nomor'];

// Ambil id_user dari nomor
$stmt = $conn->prepare("SELECT id_user FROM user WHERE nomor_user = ?");
$stmt->bind_param("s", $nomor);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$id_penerima = $row ? (int)$row['id_user'] : null;

// Validasi
if (!$id_penerima) {
    echo "Nomor tidak ditemukan!";
    exit;
}

// Simpan kontak
$sql = "INSERT INTO kontak (id_user, nama, nomor, id_penerima) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $id_user, $nama, $nomor, $id_penerima);

if ($stmt->execute()) {
    echo "Kontak berhasil disimpan!";
} else {
    echo "Gagal menyimpan kontak.";
}

$stmt->close();
$conn->close();
?>


MASALAHNYA DI BAGIAN ID_PENERIMAN DI TABEL KONTAK KENAPA SAMA