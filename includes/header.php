<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
$base = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#0b1120">
  <title>Quiz Pengetahuan Umum</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
</head>
<body>
  <div class="app-shell">
    <nav class="navbar navbar-expand-lg navbar-light py-3">
      <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base; ?>/index.php">Quiz Umum</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav ms-auto gap-2">
            <?php if (is_logged_in()): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/pages/dashboard.php">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/pages/quiz.php">Mulai Kuis</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/pages/leaderboard.php">Leaderboard</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/pages/profile.php">Profil</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/pages/about.php">Tentang</a></li>
              <li class="nav-item"><a class="btn btn-sm btn-outline-secondary" href="<?php echo $base; ?>/logout.php">Logout</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/login.php">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>/register.php">Register</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
