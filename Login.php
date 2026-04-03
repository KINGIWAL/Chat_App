<?php
session_start();
// Koneksi ke database
$conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk ambil data user berdasarkan username
$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah user ada
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verifikasi password dengan hash di database
    if (password_verify($password, $row['password'])) {
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['username'] = $row['username'];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" =>"Password salah!"]);
    }
} else {
    echo "Username tidak ditemukan!";
}

$stmt->close();
$conn->close();
?>