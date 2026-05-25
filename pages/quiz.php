<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
$user = $_SESSION['user'];
?>
<div class="quiz-page spider-page min-vh-100 py-5">
  <div class="container">
    <div class="quiz-card spider-card rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
      <div class="quiz-header d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
          <span class="hero-label"><i class="fa-solid fa-bolt"></i> Tantangan Umum</span>
          <h3 class="mb-1 mt-3"><i class="fa-solid fa-spider-web me-2"></i>Kuis Pengetahuan Umum</h3>
          <p class="text-muted">10 soal pilihan ganda, 7 menit, dan banyak trivia seru untuk ditebak.</p>
          <div class="mt-3 d-flex flex-wrap gap-2">
            <span class="feature-pill"><i class="fa-solid fa-check"></i> 10 soal acak</span>
            <span class="feature-pill"><i class="fa-solid fa-clock"></i> 7 menit</span>
            <span class="feature-pill"><i class="fa-solid fa-star"></i> Skor +10</span>
          </div>
        </div>
        <div class="text-end">
          <span class="badge bg-secondary">Waktu tersisa</span>
          <h4 id="timer" class="mt-1">07:00</h4>
        </div>
      </div>

      <div class="progress mb-4" style="height: 14px;">
        <div id="quizProgress" class="progress-bar bg-gradient" role="progressbar" style="width: 0%;"></div>
      </div>

      <div id="questionArea" class="question-area">
        <div class="question-title mb-3">
          <span class="badge bg-primary">Soal <span id="currentIndex">1</span>/10</span>
          <div class="mt-3">
            <span id="questionDomain" class="badge bg-info text-dark">Memuat domain...</span>
          </div>
          <h4 id="questionText" class="mt-3">Memuat soal...</h4>
        </div>
        <div id="optionButtons" class="list-group"></div>
      </div>

      <div class="quiz-footer d-flex justify-content-between align-items-center mt-4">
        <div>
          <span class="text-muted">Skor saat ini:</span>
          <span id="scoreValue" class="fw-semibold">0</span>
        </div>
        <button id="restartQuiz" class="btn btn-outline-secondary">Ulang Kuis</button>
      </div>
    </div>
  </div>
</div>
<?php
$pageScript = BASE_URL . '/assets/js/quiz.js?v=' . filemtime(__DIR__ . '/../assets/js/quiz.js');
require_once __DIR__ . '/../includes/footer.php';
?>
