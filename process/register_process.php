<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$nama = sanitize($_POST['nama'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if (!$nama || !$username || !$email || !$password || !$passwordConfirm) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header('Location: ../register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter.';
    header('Location: ../register.php');
    exit;
}

if ($password !== $passwordConfirm) {
    $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok.';
    header('Location: ../register.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username OR email = :email');
    if (!$stmt) {
        throw new Exception('Prepared statement gagal untuk cek user.');
    }
    $stmt->execute(['username' => $username, 'email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = 'Username atau email sudah terdaftar.';
        header('Location: ../register.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (nama, username, email, password) VALUES (:nama, :username, :email, :password)');
    if (!$stmt) {
        throw new Exception('Prepared statement gagal untuk insert user.');
    }
    
    $result = $stmt->execute(['nama' => $nama, 'username' => $username, 'email' => $email, 'password' => $hash]);
    if (!$result) {
        throw new Exception('Gagal menyimpan data user ke database.');
    }

    $_SESSION['success'] = 'Pendaftaran berhasil. Silakan login.';
    header('Location: ../login.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan database: ' . htmlspecialchars($e->getMessage());
    header('Location: ../register.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
    header('Location: ../register.php');
    exit;
}
