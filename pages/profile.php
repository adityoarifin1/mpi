<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
$user = $_SESSION['user'];

$stmt = $pdo->prepare('SELECT COUNT(*) AS total_games, COALESCE(MAX(score), 0) AS best_score, COALESCE(AVG(score), 0) AS average_score FROM scores WHERE user_id = :user_id');
$stmt->execute(['user_id' => $user['id']]);
$stats = $stmt->fetch();
$totalGames = $stats['total_games'];
$bestScore = $stats['best_score'];
$averageScore = round($stats['average_score'], 1);

$chartStmt = $pdo->prepare('SELECT score, created_at FROM scores WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 8');
$chartStmt->execute(['user_id' => $user['id']]);
$chartRows = array_reverse($chartStmt->fetchAll());
$chartLabels = array_map(fn($row) => date('d M', strtotime($row['created_at'])), $chartRows);
$chartValues = array_map(fn($row) => $row['score'], $chartRows);
$flashError = flash_message('error');
$flashSuccess = flash_message('success');
?>
<div class="container py-5">
  <div class="profile-page rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="profile-summary p-4 rounded-4 shadow-sm text-center">
          <?php
            $fotoPath = htmlspecialchars($user['foto']);
            if ($fotoPath && strpos($fotoPath, 'http') !== 0 && strpos($fotoPath, '/') !== 0) {
              $fotoPath = $base . '/' . $fotoPath;
            }
          ?>
          <img src="<?php echo $fotoPath; ?>" class="rounded-circle avatar-lg mb-3" alt="Profile">
          <h4><?php echo htmlspecialchars($user['nama']); ?></h4>
          <p class="text-muted mb-1">@<?php echo htmlspecialchars($user['username']); ?></p>
          <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
          <?php if (!empty($user['bio'])): ?>
            <p class="bio-text text-muted mt-3"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-8">
        <?php if ($flashError): ?>
          <div class="alert alert-danger"><?php echo $flashError; ?></div>
        <?php endif; ?>
        <?php if ($flashSuccess): ?>
          <div class="alert alert-success"><?php echo $flashSuccess; ?></div>
        <?php endif; ?>
        <div class="row g-4">
          <div class="col-sm-4">
            <div class="stat-card p-3 rounded-4 text-center">
              <small class="text-muted">Total Bermain</small>
              <h3 class="mt-2"><?php echo $totalGames; ?></h3>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="stat-card p-3 rounded-4 text-center">
              <small class="text-muted">Best Score</small>
              <h3 class="mt-2"><?php echo $bestScore; ?></h3>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="stat-card p-3 rounded-4 text-center">
              <small class="text-muted">Rata-rata</small>
              <h3 class="mt-2"><?php echo $averageScore; ?></h3>
            </div>
          </div>
        </div>
        <div class="profile-form-card mt-4 p-4 rounded-4 shadow-sm">
          <h5 class="mb-3">Edit Profil</h5>
          <form action="<?php echo $base; ?>/process/update_profile.php" method="post" enctype="multipart/form-data">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Upload Foto Profil</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-muted">Maksimal 2MB. JPG, PNG, GIF, WebP.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
              </div>
              <div class="col-md-6">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru">
              </div>
              <div class="col-12">
                <div class="minimal-card p-3 rounded-4 mt-3">
                  <label class="form-label">Deskripsi Diri</label>
                  <textarea id="bioField" name="bio" maxlength="1000" class="form-control" rows="4" placeholder="Tulis sedikit tentang diri Anda..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                  <div class="form-text text-muted mt-2"><span id="bioCount">0</span>/1000 karakter</div>
                </div>
              </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-4">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
          <form action="<?php echo $base; ?>/process/delete_account.php" method="post" onsubmit="return confirm('Yakin ingin menghapus akun? Semua data akan hilang.');" class="mt-3">
            <button type="submit" class="btn btn-danger">Hapus Akun</button>
          </form>
        </div>
        <div class="chart-card mt-4 p-4 rounded-4 shadow-sm">
          <h5>Statistik Skor Terakhir</h5>
          <canvas id="scoreChart" height="160"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  const labels = <?php echo json_encode($chartLabels); ?>;
  const data = <?php echo json_encode($chartValues); ?>;
  window.addEventListener('load', function() {
    const ctx = document.getElementById('scoreChart');
    if (!ctx || typeof Chart === 'undefined') {
      return;
    }

    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Skor',
          data,
          borderColor: '#eef2ff',
          backgroundColor: 'rgba(238, 242, 255, 0.18)',
          pointBackgroundColor: '#eef2ff',
          pointBorderColor: '#ffffff',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true, max: 100 }
        }
      }
    });
  });
</script>
<script>
  // Bio character counter
  document.addEventListener('DOMContentLoaded', function() {
    const bioField = document.getElementById('bioField');
    const bioCount = document.getElementById('bioCount');
    if (bioField && bioCount) {
      const update = () => bioCount.textContent = bioField.value.length;
      update();
      bioField.addEventListener('input', update);
    }
  });
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
