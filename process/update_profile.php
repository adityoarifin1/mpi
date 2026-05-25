<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user = $_SESSION['user'];

$nama = sanitize($_POST['nama'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$uploadFile = $_FILES['foto'] ?? null;
$foto = $user['foto'] ?? 'assets/img/default-avatar.svg';
$bio = sanitize($_POST['bio'] ?? '');

if (!$nama || !$username || !$email) {
    $_SESSION['error'] = 'Nama, username, dan email wajib diisi.';
    header('Location: ../pages/profile.php');
    exit;
}

if ($newPassword || $confirmPassword) {
    if (strlen($newPassword) < 6) {
        $_SESSION['error'] = 'Password baru minimal 6 karakter.';
        header('Location: ../pages/profile.php');
        exit;
    }
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'Password baru dan konfirmasi password tidak cocok.';
        header('Location: ../pages/profile.php');
        exit;
    }
}

// Batasi panjang bio
if (mb_strlen($bio) > 1000) {
    $_SESSION['error'] = 'Deskripsi diri maksimal 1000 karakter.';
    header('Location: ../pages/profile.php');
    exit;
}

if ($uploadFile && $uploadFile['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($uploadFile['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Terjadi kesalahan saat mengunggah foto profil.';
        header('Location: ../pages/profile.php');
        exit;
    }

    if ($uploadFile['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = 'Ukuran foto maksimal 2MB.';
        header('Location: ../pages/profile.php');
        exit;
    }

    $imageInfo = getimagesize($uploadFile['tmp_name']);
    if ($imageInfo === false) {
        $_SESSION['error'] = 'File yang diunggah bukan gambar valid.';
        header('Location: ../pages/profile.php');
        exit;
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    $mimeType = $imageInfo['mime'];
    if (!isset($allowedTypes[$mimeType])) {
        $_SESSION['error'] = 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.';
        header('Location: ../pages/profile.php');
        exit;
    }

    $uploadDir = __DIR__ . '/../assets/img/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExt = $allowedTypes[$mimeType];
    $fileName = sprintf('profile_%s_%s.%s', $user['id'], time(), $fileExt);
    $destination = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($uploadFile['tmp_name'], $destination)) {
        $_SESSION['error'] = 'Gagal menyimpan foto profil.';
        header('Location: ../pages/profile.php');
        exit;
    }

    if (!empty($user['foto']) && strpos($user['foto'], 'assets/img/uploads/') === 0) {
        $oldFile = __DIR__ . '/../' . $user['foto'];
        if (is_file($oldFile)) {
            @unlink($oldFile);
        }
    }

    $foto = 'assets/img/uploads/' . $fileName;
}

try {
    // Pastikan kolom `bio` ada di tabel users; buat jika belum
    $colStmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'bio'");
    $colStmt->execute();
    $hasBio = (bool) $colStmt->fetchColumn();
    if (!$hasBio) {
        $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT DEFAULT '' AFTER foto");
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email) AND id != :id');
    $stmt->execute(['username' => $username, 'email' => $email, 'id' => $user['id']]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = 'Username atau email sudah digunakan oleh akun lain.';
        header('Location: ../pages/profile.php');
        exit;
    }

    if ($newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql = 'UPDATE users SET nama = :nama, username = :username, email = :email, foto = :foto, bio = :bio, password = :password WHERE id = :id';
    } else {
        $updateSql = 'UPDATE users SET nama = :nama, username = :username, email = :email, foto = :foto, bio = :bio WHERE id = :id';
    }

    $updateStmt = $pdo->prepare($updateSql);
    $params = [
        'nama' => $nama,
        'username' => $username,
        'email' => $email,
        'foto' => $foto,
        'bio' => $bio,
        'id' => $user['id'],
    ];

    if ($newPassword) {
        $params['password'] = $passwordHash;
    }

    $updateResult = $updateStmt->execute($params);
    if (!$updateResult) {
        throw new Exception('Gagal memperbarui profil.');
    }

    $refreshStmt = $pdo->prepare('SELECT id, nama, username, email, foto, role, bio FROM users WHERE id = :id');
    $refreshStmt->execute(['id' => $user['id']]);
    $updatedUser = $refreshStmt->fetch();
    if (!$updatedUser) {
        throw new Exception('Gagal memuat kembali data user.');
    }

    unset($updatedUser['password']);
    $_SESSION['user'] = $updatedUser;
    $_SESSION['success'] = 'Profil berhasil diperbarui.';
    header('Location: ../pages/profile.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan database: ' . htmlspecialchars($e->getMessage());
    header('Location: ../pages/profile.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
    header('Location: ../pages/profile.php');
    exit;
}
