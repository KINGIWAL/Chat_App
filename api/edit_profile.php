<?php
require __DIR__ . '/db.php';
session_start();

// Ambil id_user dari session (pastikan saat login kamu simpan id_user di $_SESSION)
$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    echo "User tidak ditemukan, silakan login ulang.";
    exit;
}

$username    = $_POST['username'] ?? '';
$pathDB      = null;

// Jika ada file foto baru diupload
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fileTmp  = $_FILES['foto']['tmp_name'];
    $fileName = basename($_FILES['foto']['name']);
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array($fileExt, $allowed)) {
        die("Format file tidak diizinkan");
    }

    $newName   = uniqid('user_', true) . '.' . $fileExt;
    $uploadDir = dirname(__DIR__) . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $uploadPath = $uploadDir . $newName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $pathDB = 'uploads/' . $newName;
    } else {
        die("Gagal menyimpan file foto");
    }
}

// Update ke database
try {
    if ($pathDB) {
        // Jika ada foto baru
        $sql = "UPDATE user SET username = ?,  foto_profile = ? WHERE id_user = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$username, $pathDB, $id_user]);
    } else {
        // Jika tidak ada foto baru
        $sql = "UPDATE user SET username = ? WHERE id_user =  ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$username, $id_user]);
    }

    if ($success) {
        echo "Profil berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui profil.";
    }
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo "Terjadi kesalahan saat update profil.";
}
