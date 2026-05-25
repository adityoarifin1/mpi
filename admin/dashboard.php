<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$totalUsers = 0;
$totalQuestions = 0;
$totalScores = 0;
$bestScore = 0;

try {
    $totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal mengambil data total user.';
}

try {
    $totalQuestions = $pdo->query('SELECT COUNT(*) FROM questions')->fetchColumn();
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal mengambil data total soal.';
}

try {
    $totalScores = $pdo->query('SELECT COUNT(*) FROM scores')->fetchColumn();
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal mengambil data total skor.';
}

try {
    $bestScore = $pdo->query('SELECT COALESCE(MAX(score),0) FROM scores')->fetchColumn();
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal mengambil skor tertinggi.';
}
?>
<div class="container py-5">
  <div class="admin-overview animate__animated animate__fadeInUp">
    <div class="admin-hero p-4 p-md-5 rounded-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
            <span class="hero-badge admin-badge">Mode Admin</span>
            <span class="hero-badge admin-user-pill"><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'admin'); ?></span>
          </div>
          <h2 class="mb-2">Admin Dashboard</h2>
          <p class="text-muted mb-4">Pantau pengguna, perbarui bank soal, dan cek performa permainan dari satu tempat.</p>
          <div class="d-flex flex-wrap gap-3">
            <a href="users.php" class="btn btn-primary"><i class="fa-solid fa-users me-2"></i>Kelola User</a>
            <a href="questions.php" class="btn btn-outline-light"><i class="fa-solid fa-file-pen me-2"></i>Kelola Soal</a>
            <a href="scores.php" class="btn btn-outline-light"><i class="fa-solid fa-chart-simple me-2"></i>Lihat Skor</a>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="admin-highlight-card p-4 rounded-4">
            <p class="text-uppercase small mb-2 text-muted">Status hari ini</p>
            <h3 class="mb-1">Rapi & aman</h3>
            <p class="mb-0 text-muted">Panel admin siap digunakan untuk mengelola konten dan aktivitas pengguna.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-3">
      <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card p-4 rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="admin-stat-icon"><i class="fa-solid fa-users"></i></span>
            <span class="admin-stat-label">User</span>
          </div>
          <h3><?php echo $totalUsers; ?></h3>
          <p class="mb-0 text-muted">Total pengguna terdaftar</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card p-4 rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="admin-stat-icon"><i class="fa-solid fa-circle-question"></i></span>
            <span class="admin-stat-label">Soal</span>
          </div>
          <h3><?php echo $totalQuestions; ?></h3>
          <p class="mb-0 text-muted">Total bank soal aktif</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card p-4 rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="admin-stat-icon"><i class="fa-solid fa-medal"></i></span>
            <span class="admin-stat-label">Skor</span>
          </div>
          <h3><?php echo $totalScores; ?></h3>
          <p class="mb-0 text-muted">Total sesi permainan</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card p-4 rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="admin-stat-icon"><i class="fa-solid fa-arrow-trend-up"></i></span>
            <span class="admin-stat-label">Top Score</span>
          </div>
          <h3><?php echo $bestScore; ?></h3>
          <p class="mb-0 text-muted">Skor tertinggi tercatat</p>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-3">
      <div class="col-lg-4">
        <div class="admin-quick-panel p-4 rounded-4">
          <h5 class="mb-3">Menu cepat</h5>
          <div class="d-grid gap-2">
            <a href="users.php" class="admin-quick-link"><i class="fa-solid fa-user-gear"></i>Kelola User</a>
            <a href="questions.php" class="admin-quick-link"><i class="fa-solid fa-wand-magic-sparkles"></i>Tambah & edit soal</a>
            <a href="scores.php" class="admin-quick-link"><i class="fa-solid fa-ranking-star"></i>Lihat hasil kuis</a>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="admin-quick-panel p-4 rounded-4 h-100">
          <h5 class="mb-3">Panduan singkat</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="admin-guide-item p-3 rounded-4">
                <h6><i class="fa-solid fa-users me-2"></i>Manage user</h6>
                <p class="mb-0 text-muted">Pantau akun pengguna dan pastikan role sesuai.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="admin-guide-item p-3 rounded-4">
                <h6><i class="fa-solid fa-file-pen me-2"></i>Manage questions</h6>
                <p class="mb-0 text-muted">Perbarui bank soal dan klasifikasi domain dengan mudah.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="admin-guide-item p-3 rounded-4">
                <h6><i class="fa-solid fa-chart-line me-2"></i>Monitor skor</h6>
                <p class="mb-0 text-muted">Lihat performa pemain serta skor tertinggi dari waktu ke waktu.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="admin-guide-item p-3 rounded-4">
                <h6><i class="fa-solid fa-shield-halved me-2"></i>Kontrol akses</h6>
                <p class="mb-0 text-muted">Admin tetap berada pada panel terpisah dari halaman pengguna biasa.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
