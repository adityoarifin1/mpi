<?php
require_once __DIR__ . '/includes/header.php';
$flash = flash_message('error');
?>
<div class="auth-page d-flex align-items-center justify-content-center min-vh-100">
  <div class="auth-layout row gx-4 gy-4 align-items-center w-100">
    <div class="col-lg-6 d-none d-lg-block">
      <div class="auth-hero p-4 animate__animated animate__fadeInLeft">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80" alt="Ilustrasi belajar kuis" class="img-fluid rounded-4 shadow-lg">
      </div>
    </div>
    <div class="col-lg-6">
      <div class="auth-card animate__animated animate__fadeInUp">
        <div class="text-center mb-4">
          <h1 class="mb-2">Buat Akun Quiz Pengetahuan Umum</h1>
          <p class="text-muted">Gabung sekarang untuk tantangan pengetahuan umum setiap hari.</p>
        </div>
        <?php if ($flash): ?>
          <div class="alert alert-danger"><?php echo $flash; ?></div>
        <?php endif; ?>
        <form action="process/register_process.php" method="post" class="form-row">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirm" class="form-control" placeholder="Ulangi password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-lg w-100">Daftar Sekarang</button>
      <div class="text-center text-muted mt-3">
        Sudah punya akun? <a href="login.php">Login di sini</a>
      </div>
    </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
