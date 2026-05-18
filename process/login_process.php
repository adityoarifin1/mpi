<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$identity = sanitize($_POST['identity'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (!$identity || !$password) {
    $_SESSION['error'] = 'Username/Email dan password wajib diisi.';
    header('Location: ../login.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :identity OR email = :identity2 LIMIT 1');
    if (!$stmt) {
        throw new Exception('Prepared statement gagal untuk query user.');
    }
    $stmt->execute(['identity' => $identity, 'identity2' => $identity]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Username/email atau password salah.';
        header('Location: ../login.php');
        exit;
    }

    unset($user['password']);
    $_SESSION['user'] = $user;
    if ($remember) {
        setcookie('remember_user', $user['username'], time() + 60 * 60 * 24 * 30, '/');
    }
    header('Location: ../pages/dashboard.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan database: ' . htmlspecialchars($e->getMessage());
    header('Location: ../login.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
    header('Location: ../login.php');
    exit;
}
