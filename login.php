<?php
require_once __DIR__ . '/includes/header.php';
$flash = flash_message('error');
$success = flash_message('success');
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
          <h1 class="mb-2">Masuk ke Kuis Pengetahuan Umum</h1>
          <p class="text-muted">Akses quiz umum modern dengan UI premium dan tantangan cepat.</p>
        </div>
        <?php if ($flash): ?>
          <div class="alert alert-danger"><?php echo $flash; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="process/login_process.php" method="post" class="form-row">
      <div class="mb-3">
        <label class="form-label">Username atau Email</label>
        <input type="text" name="identity" class="form-control" placeholder="Masukkan username atau email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck">
          <label class="form-check-label" for="rememberCheck">Ingat saya</label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
      </div>
      <div class="text-center text-muted">
        Belum punya akun? <a href="register.php">Daftar sekarang</a>
      </div>
    </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
