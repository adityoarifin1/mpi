  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <?php $base = defined('BASE_URL') ? BASE_URL : ''; ?>
  <script src="<?php echo $base; ?>/assets/js/main.js"></script>
  <?php if (!empty($pageScript)): ?>
    <script src="<?php echo $pageScript; ?>"></script>
  <?php endif; ?>
</body>
</html>
