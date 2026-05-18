<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
$user = $_SESSION['user'];

$stmt = $pdo->prepare('SELECT COUNT(*) AS total_games, COALESCE(MAX(score), 0) AS best_score FROM scores WHERE user_id = :user_id');
$stmt->execute(['user_id' => $user['id']]);
$data = $stmt->fetch();
$totalGames = $data['total_games'] ?? 0;
$best_score = $data['best_score'] ?? 0;
?>
<div class="container py-5">
  <div id="toastData" data-message="Selamat datang kembali, <?php echo htmlspecialchars($user['nama']); ?>!" hidden></div>
  <div class="hero-banner row align-items-center gx-5 mb-5">
    <div class="col-lg-7">
      <div class="hero-panel p-5">
        <span class="hero-label"><i class="fa-solid fa-brain"></i> Kuis Pengetahuan Umum</span>
        <h1 class="display-5 mt-4">Latih Otakmu dengan Tantangan Umum</h1>
        <p class="text-muted mt-3">Jawab 10 soal dari berbagai topik umum, kumpulkan skor, dan menangkan leaderboard dengan tampilan modern yang penuh energi.</p>
        <div class="mt-4 d-flex flex-wrap gap-2">
          <span class="feature-pill"><i class="fa-solid fa-stopwatch"></i> 7 menit</span>
          <span class="feature-pill"><i class="fa-solid fa-star"></i> +10 poin benar</span>
          <span class="feature-pill"><i class="fa-solid fa-trophy"></i> Papan peringkat</span>
          <span class="feature-pill"><i class="fa-solid fa-shield-check"></i> Aman & responsif</span>
        </div>
      </div>
    </div>
    <div class="col-lg-5 text-center">
      <div class="hero-panel p-4">
        <img src="<?php echo BASE_URL; ?>/assets/img/hero-quiz.svg" alt="Quiz Hero" class="img-fluid animate__animated animate__zoomIn">
      </div>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
      <div>
        <span class="badge bg-primary rounded-pill">Dashboard</span>
        <h2 class="mt-3">Halo, <?php echo htmlspecialchars($user['nama']); ?>!</h2>
        <p class="text-muted">Selamat datang di Kuis Pengetahuan Umum — jawab dan kalahkan skor terbaikmu!</p>
      </div>
      <div class="profile-card text-end">
        <img src="<?php echo htmlspecialchars($user['foto']); ?>" alt="Profile" class="avatar rounded-circle mb-2">
        <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($user['username']); ?></p>
        <small class="text-muted">Role: <?php echo htmlspecialchars($user['role']); ?></small>
      </div>
    </div>

    <div class="col-md-4">
      <div class="info-card p-4 shadow-sm rounded-4 animate__animated animate__fadeInUp">
        <h5>Total Permainan</h5>
        <p class="display-6 mb-0"><?php echo $totalGames; ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="info-card p-4 shadow-sm rounded-4 animate__animated animate__fadeInUp animate__delay-1s">
        <h5>Best Score</h5>
        <p class="display-6 mb-0"><?php echo $best_score; ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="info-card p-4 shadow-sm rounded-4 animate__animated animate__fadeInUp animate__delay-2s">
        <h5>Jam Saat Ini</h5>
        <p id="clock" class="display-6 mb-0">--:--:--</p>
      </div>
    </div>

    <div class="col-12">
      <div class="row g-4">
        <div class="col-md-4">
          <a href="quiz.php" class="menu-card p-4 rounded-4 text-decoration-none d-block shadow-sm">
            <div class="icon-circle bg-primary text-white mb-3"><i class="fa-solid fa-play"></i></div>
            <h5>Mulai Kuis</h5>
            <p class="text-muted">Kerjakan 10 soal acak dalam 7 menit.</p>
          </a>
        </div>
        <div class="col-md-4">
          <a href="leaderboard.php" class="menu-card p-4 rounded-4 text-decoration-none d-block shadow-sm">
            <div class="icon-circle bg-success text-white mb-3"><i class="fa-solid fa-trophy"></i></div>
            <h5>Leaderboard</h5>
            <p class="text-muted">Lihat ranking top 10 pemain terbaik.</p>
          </a>
        </div>
        <div class="col-md-4">
          <a href="profile.php" class="menu-card p-4 rounded-4 text-decoration-none d-block shadow-sm">
            <div class="icon-circle bg-warning text-white mb-3"><i class="fa-solid fa-user"></i></div>
            <h5>Profil</h5>
            <p class="text-muted">Kelola profil dan lihat statistik nilai.</p>
          </a>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="about-card p-4 rounded-4 shadow-sm bg-gradient">
        <h5>Tentang Quiz Umum</h5>
        <p class="mb-0">Kuis ini dirancang untuk menguji pengetahuan umum dengan gameplay cepat, skor, dan leaderboard terbaik.</p>
        <a href="about.php" class="btn btn-outline-light mt-3">Pelajari Lebih Lanjut</a>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
