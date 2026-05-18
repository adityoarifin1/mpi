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
?>
<div class="container py-5">
  <div class="profile-page rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="profile-summary p-4 rounded-4 shadow-sm text-center">
          <img src="<?php echo htmlspecialchars($user['foto']); ?>" class="rounded-circle avatar-lg mb-3" alt="Profile">
          <h4><?php echo htmlspecialchars($user['nama']); ?></h4>
          <p class="text-muted mb-1">@<?php echo htmlspecialchars($user['username']); ?></p>
          <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
      </div>
      <div class="col-lg-8">
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
  const ctx = document.getElementById('scoreChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Skor',
          data,
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79, 70, 229, 0.15)',
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
  }
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
