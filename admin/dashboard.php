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
  <div class="admin-overview rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
      <div>
        <h2>Admin Dashboard</h2>
        <p class="text-muted">Kelola user, soal, dan statistik permainan.</p>
      </div>
      <a href="../logout.php" class="btn btn-outline-danger">Logout Admin</a>
    </div>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="admin-card p-4 rounded-4 bg-light text-center">
          <small>Total User</small>
          <h3 class="mt-2"><?php echo $totalUsers; ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="admin-card p-4 rounded-4 bg-light text-center">
          <small>Total Soal</small>
          <h3 class="mt-2"><?php echo $totalQuestions; ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="admin-card p-4 rounded-4 bg-light text-center">
          <small>Total Skor Tercatat</small>
          <h3 class="mt-2"><?php echo $totalScores; ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="admin-card p-4 rounded-4 bg-light text-center">
          <small>Skor Tertinggi</small>
          <h3 class="mt-2"><?php echo $bestScore; ?></h3>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
