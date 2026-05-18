<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
?>
<div class="container py-5">
  <div class="about-card rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="row g-4">
      <div class="col-lg-8">
        <h2>Tentang Kuis Pengetahuan Umum</h2>
        <p class="text-muted">Quiz Umum ini membantu Anda mengasah pengetahuan seputar sejarah, sains, geografi, dan budaya secara cepat dan seru.</p>
        <h5>Cara Bermain</h5>
        <ul>
          <li>Pilih menu Mulai Kuis.</li>
          <li>Kerjakan 10 soal acak dalam waktu 7 menit.</li>
          <li>Pilih jawaban A/B/C/D dan sistem akan memvalidasi otomatis.</li>
          <li>Kumpulkan skor dan lihat leaderboard.</li>
        </ul>
        <h5>Fitur Utama</h5>
        <ul>
          <li>Timer realtime 7 menit.</li>
          <li>Skor otomatis dan hasil akhir.</li>
          <li>Leaderboard dan statistik profil.</li>
          <li>Admin panel untuk CRUD soal dan user.</li>
        </ul>
      </div>
      <div class="col-lg-4">
        <div class="developer-card rounded-4 p-4 bg-primary text-white shadow-sm">
          <h5>Developer</h5>
          <p>Fullstack Web Developer profesional menciptakan aplikasi yang bersih, responsif, dan modern.</p>
          <p class="mb-0"><i class="fa-solid fa-envelope me-2"></i>developer@example.com</p>
          <p><i class="fa-solid fa-phone me-2"></i>+62 812-3456-7890</p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
