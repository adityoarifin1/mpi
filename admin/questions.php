<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$action = $_POST['action'] ?? '';
if ($action === 'create') {
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correct = strtoupper(trim($_POST['correct_answer'] ?? ''));

    if ($question && $optionA && $optionB && $optionC && $optionD && in_array($correct, ['A','B','C','D'])) {
        try {
            $stmt = $pdo->prepare('INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES (:question, :a, :b, :c, :d, :correct)');
            if (!$stmt) {
                throw new Exception('Prepared statement gagal.');
            }
            $result = $stmt->execute(['question' => $question, 'a' => $optionA, 'b' => $optionB, 'c' => $optionC, 'd' => $optionD, 'correct' => $correct]);
            if (!$result) {
                throw new Exception('Gagal menyimpan soal.');
            }
            $_SESSION['success'] = 'Soal berhasil ditambahkan.';
            header('Location: questions.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal menambahkan soal: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $_SESSION['error'] = 'Semua field wajib diisi dan jawaban benar harus A, B, C, atau D.';
    }
}

if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM questions WHERE id = :id');
        if (!$stmt) {
            throw new Exception('Prepared statement gagal.');
        }
        $result = $stmt->execute(['id' => $_GET['delete']]);
        if (!$result) {
            throw new Exception('Gagal menghapus soal.');
        }
        $_SESSION['success'] = 'Soal berhasil dihapus.';
        header('Location: questions.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Gagal menghapus soal: ' . htmlspecialchars($e->getMessage());
        header('Location: questions.php');
        exit;
    }
}

try {
    $questions = $pdo->query('SELECT * FROM questions ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $questions = [];
    $_SESSION['error'] = 'Gagal mengambil daftar soal.';
}
?>
<div class="container py-5">
  <div class="admin-list rounded-4 p-4 shadow-sm animate__animated animate__fadeInUp">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Kelola Soal</h2>
      <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="table-responsive mb-4">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Soal</th>
            <th>Jawaban Benar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($questions as $question): ?>
            <tr>
              <td><?php echo $question['id']; ?></td>
              <td><?php echo htmlspecialchars(substr($question['question'], 0, 70)); ?>...</td>
              <td><?php echo $question['correct_answer']; ?></td>
              <td>
                <a href="questions.php?delete=<?php echo $question['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus soal ini?');">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card p-4 rounded-4 bg-light">
      <h4>Tambah Soal Baru</h4>
      <form method="post" class="row g-3 mt-2">
        <input type="hidden" name="action" value="create">
        <div class="col-12">
          <label class="form-label">Pertanyaan</label>
          <textarea name="question" class="form-control" rows="3" required></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Pilihan A</label>
          <input type="text" name="option_a" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Pilihan B</label>
          <input type="text" name="option_b" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Pilihan C</label>
          <input type="text" name="option_c" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Pilihan D</label>
          <input type="text" name="option_d" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Jawaban Benar</label>
          <select name="correct_answer" class="form-select" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
