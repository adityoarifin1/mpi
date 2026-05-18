<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$records = [];
try {
    $records = $pdo->query('SELECT s.score, s.correct_answers, s.wrong_answers, s.completion_time, s.created_at, u.nama, u.username FROM scores s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC LIMIT 50')->fetchAll();
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal mengambil data skor: ' . htmlspecialchars($e->getMessage());
}
?>
<div class="container py-5">
  <div class="admin-list rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Data Skor</h2>
      <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Skor</th>
            <th>Benar</th>
            <th>Salah</th>
            <th>Waktu</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($records as $index => $record): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($record['nama']); ?></td>
              <td><?php echo htmlspecialchars($record['username']); ?></td>
              <td><?php echo htmlspecialchars($record['score']); ?></td>
              <td><?php echo htmlspecialchars($record['correct_answers']); ?></td>
              <td><?php echo htmlspecialchars($record['wrong_answers']); ?></td>
              <td><?php echo htmlspecialchars($record['completion_time']); ?></td>
              <td><?php echo date('d M Y H:i', strtotime($record['created_at'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
