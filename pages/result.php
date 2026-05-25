<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
$user = $_SESSION['user'];

$stmt = $pdo->prepare('SELECT * FROM scores WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1');
$stmt->execute(['user_id' => $user['id']]);
$result = $stmt->fetch();
if (!$result) {
    header('Location: dashboard.php');
    exit;
}

$grade = 'Belajar Lagi';
if ($result['score'] >= 90) {
    $grade = 'Sangat Baik';
} elseif ($result['score'] >= 80) {
    $grade = 'Baik';
} elseif ($result['score'] >= 70) {
    $grade = 'Cukup';
}
?>
<div class="container py-5">
  <div class="result-card minimal-card rounded-4 p-4 shadow-sm animate__animated animate__zoomIn">
    <div class="text-center mb-4">
      <h2>Hasil Kuis</h2>
      <p class="text-muted">Kerja bagus, <?php echo htmlspecialchars($user['nama']); ?>. Berikut ringkasan permainan Anda.</p>
    </div>
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="stat-card p-4 rounded-4 text-center">
          <span class="text-muted">Total Skor</span>
          <h3 class="mt-2"><?php echo htmlspecialchars($result['score']); ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card p-4 rounded-4 text-center">
          <span class="text-muted">Jawaban Benar</span>
          <h3 class="mt-2"><?php echo htmlspecialchars($result['correct_answers']); ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card p-4 rounded-4 text-center">
          <span class="text-muted">Jawaban Salah</span>
          <h3 class="mt-2"><?php echo htmlspecialchars($result['wrong_answers']); ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card p-4 rounded-4 text-center">
          <span class="text-muted">Waktu</span>
          <h3 class="mt-2"><?php echo htmlspecialchars($result['completion_time']); ?></h3>
        </div>
      </div>
    </div>
    <div class="text-center mb-4">
      <h4 class="badge bg-success px-4 py-3">Grade: <?php echo $grade; ?></h4>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
      <a href="quiz.php" class="btn btn-primary btn-lg">Ulang Kuis</a>
      <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">Kembali Dashboard</a>
      <a href="leaderboard.php" class="btn btn-outline-success btn-lg">Lihat Leaderboard</a>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
