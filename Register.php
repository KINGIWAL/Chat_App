<?php
$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Generate nomor_user random 14 digit
$nomor_user = str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);

// Insert ke database
$sql = "INSERT INTO user (username, password, nomor_user) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $password, $nomor_user);

if ($stmt->execute()) {
    echo "Registrasi berhasil! Nomor user: " . $nomor_user;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>