<?php
require __DIR__ . '/db.php';
session_start();

$username = $_POST['username'] ?? '';
$password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);

// Generate nomor_user random 14 digit
$nomor_user = str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['foto']['tmp_name'];
        $fileName = basename($_FILES['foto']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($fileExt, $allowed)) {
            die("Format file tidak diizinkan");
        }

        $newName = uniqid('user_', true) . '.' . $fileExt;
        $uploadDir =dirname(__DIR__) . '/uploads/';
        if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // buat folder kalau belum ada
        }
        $uploadPath = $uploadDir . $newName;
        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $pathDB = 'uploads/' . $newName;
        } else {
            die("Gagal menyimpan file foto");
        }
    } else {
        echo "Foto profil wajib diupload !";
        exit;
    }

try {
        // Cek username
    $checkUserSql = "SELECT COUNT(*) FROM user WHERE username = ?";
    $checkUserStmt = $pdo->prepare($checkUserSql);
    $checkUserStmt->execute([$username]);
    $userExists = $checkUserStmt->fetchColumn();

    if ($userExists > 0) {
        echo "Username sudah digunakan.";
        exit;
    }

    $checkPassSql = "SELECT password FROM user";
    $checkPassStmt = $pdo->query($checkPassSql);
    $passExists = false;

    while ($row = $checkPassStmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($_POST['password'], $row['password'])) {
            $passExists = true;
            break;
        }
    }
    if ($passExists) {
        echo "Password sudah digunakan.";
        exit;
    }

        // Pastikan nomor_user belum ada di database
    $checkNomorSql = "SELECT COUNT(*) FROM user WHERE nomor_user = ?";
    $checkNomorStmt = $pdo->prepare($checkNomorSql);
    $checkNomorStmt->execute([$nomor_user]);
    $nomorExists = $checkNomorStmt->fetchColumn();

    // Jika sudah ada, generate ulang sampai dapat nomor unik
    while ($nomorExists > 0) {
        $nomor_user = str_pad(mt_rand(0, 99999999999999), 14, '0', STR_PAD_LEFT);
        $checkNomorStmt->execute([$nomor_user]);
        $nomorExists = $checkNomorStmt->fetchColumn();
    }
     // Insert ke database
    $sql = "INSERT INTO user (username, password, nomor_user, foto_profile) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$username, $password, $nomor_user, $pathDB]);

    if ($success) {
        echo "Registrasi berhasil! Nomor user: " . htmlspecialchars($nomor_user);
    } else {
        echo "Registrasi gagal.";
    }
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo "Terjadi kesalahan saat registrasi.";
}
