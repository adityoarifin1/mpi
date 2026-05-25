<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

$query = 'SELECT s.score, s.created_at, u.nama, u.username FROM scores s JOIN users u ON s.user_id = u.id ORDER BY s.score DESC, s.created_at ASC LIMIT 50';
$stmt = $pdo->query($query);
$leaders = $stmt->fetchAll();
?>
<div class="container py-5">
  <div class="leaderboard-card minimal-card spider-card rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
      <div>
        <h2 class="minimal-title"><i class="fa-solid fa-spider-web me-2"></i> Leaderboard</h2>
        <p class="text-muted">Ranking pengguna terbaik dengan tampilan sederhana.</p>
      </div>
      <div class="search-box">
        <input id="leaderSearch" type="search" class="form-control" placeholder="Cari nama atau username...">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle text-white" style="color: #f8fafc !important;">
        <thead class="table-light">
          <tr>
            <th style="color: #f8fafc !important;">#</th>
            <th style="color: #f8fafc !important;">Nama</th>
            <th style="color: #f8fafc !important;">Username</th>
            <th style="color: #f8fafc !important;">Skor</th>
            <th style="color: #f8fafc !important;">Tanggal</th>
          </tr>
        </thead>
        <tbody id="leaderboardBody" style="color: #f8fafc !important;">
          <?php foreach ($leaders as $index => $leader): ?>
            <tr>
              <td style="color: #f8fafc !important;"><?php echo $index + 1; ?></td>
              <td style="color: #f8fafc !important;"><?php echo htmlspecialchars($leader['nama']); ?></td>
              <td style="color: #f8fafc !important;"><?php echo htmlspecialchars($leader['username']); ?></td>
              <td style="color: #f8fafc !important;"><?php echo htmlspecialchars($leader['score']); ?></td>
              <td style="color: #f8fafc !important;"><?php echo date('d M Y H:i', strtotime($leader['created_at'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
  document.getElementById('leaderSearch').addEventListener('input', function() {
    const needle = this.value.toLowerCase();
    document.querySelectorAll('#leaderboardBody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(needle) ? '' : 'none';
    });
  });
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
