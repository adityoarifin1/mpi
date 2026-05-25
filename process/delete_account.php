<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user = $_SESSION['user'];

try {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $user['id']]);
    session_destroy();
    $_SESSION = [];
    header('Location: ../login.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menghapus akun: ' . htmlspecialchars($e->getMessage());
    header('Location: ../pages/profile.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
    header('Location: ../pages/profile.php');
    exit;
}
