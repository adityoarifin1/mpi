<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id AND role != "admin"');
        if (!$stmt) {
            throw new Exception('Prepared statement gagal.');
        }
        $result = $stmt->execute(['id' => $_GET['delete']]);
        if (!$result) {
            throw new Exception('Gagal menghapus user.');
        }
        $_SESSION['success'] = 'User berhasil dihapus.';
        header('Location: users.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Gagal menghapus user: ' . htmlspecialchars($e->getMessage());
        header('Location: users.php');
        exit;
    }
}

try {
    $users = $pdo->query('SELECT id, nama, username, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
    $users = [];
    $_SESSION['error'] = 'Gagal mengambil daftar user.';
}
?>
<div class="container py-5">
  <div class="admin-list rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Kelola User</h2>
      <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Terdaftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo $user['id']; ?></td>
              <td><?php echo htmlspecialchars($user['nama']); ?></td>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['role']); ?></td>
              <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
              <td>
                <?php if ($user['role'] !== 'admin'): ?>
                  <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?');">Hapus</a>
                <?php else: ?>
                  <span class="badge bg-secondary">Admin</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
