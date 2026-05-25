<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

function infer_question_domain($question) {
    $text = strtolower(($question['question'] ?? '') . ' ' . ($question['option_a'] ?? '') . ' ' . ($question['option_b'] ?? '') . ' ' . ($question['option_c'] ?? '') . ' ' . ($question['option_d'] ?? ''));

    if (preg_match('/lawan kata|bahasa resmi|warna primer|seni|musik|budaya|literatur|humaniora|filosofi|teater|drama|puisi|tradisi|kesenian|etnis|karya|rupa|art|ritual/', $text)) {
        return 'Seni, Budaya, dan Humaniora';
    }

    if (preg_match('/planet|matahari|kimia|emas|organ|tubuh|jantung|paru|ginjal|hati|sains|lingkungan|energi|air|bumi|fotosintesis|biologi|fisika|kimia|ekologi|habitat|siklus|cuaca|iklim/', $text)) {
        return 'Sains dan Lingkungan';
    }

    if (preg_match('/teknologi|komputer|digital|internet|aplikasi|software|hardware|sistem|data|algoritma|otomatis|robot|kode|platform|digitalisasi|iot|smartphone|network|website|ui|ux/', $text)) {
        return 'Teknologi dan Digitalisasi';
    }

    if (preg_match('/ibukota|benua|gunung|negara|presiden|proklamasi|tahun|kota|bulan|sejarah|indonesia|ri|geo|geografi|global|lokasi|wilayah|negara/', $text)) {
        return 'Geografi dan Sejarah Global';
    }

    return 'Sains dan Lingkungan';
}

function normalized_correct_answer($question) {
    $stored = strtoupper(trim((string) ($question['correct_answer'] ?? '')));

    if (in_array($stored, ['A', 'B', 'C', 'D'], true)) {
        return $stored;
    }

    if (ctype_digit($stored)) {
        $letters = ['A', 'B', 'C', 'D'];
        $index = (int) $stored;
        return $letters[$index] ?? $stored;
    }

    return $stored;
}

try {
    $pdo->exec("ALTER TABLE questions ADD COLUMN IF NOT EXISTS domain VARCHAR(64) NOT NULL DEFAULT 'Sains dan Lingkungan'");
} catch (Exception $e) {
}

$action = $_POST['action'] ?? '';
$allowedDomains = [
    'Sains dan Lingkungan',
    'Geografi dan Sejarah Global',
    'Teknologi dan Digitalisasi',
    'Seni, Budaya, dan Humaniora',
];

if ($action === 'create') {
    $question = trim($_POST['question'] ?? '');
    $optionA = trim($_POST['option_a'] ?? '');
    $optionB = trim($_POST['option_b'] ?? '');
    $optionC = trim($_POST['option_c'] ?? '');
    $optionD = trim($_POST['option_d'] ?? '');
    $correct = strtoupper(trim($_POST['correct_answer'] ?? ''));
    $domain = trim($_POST['domain'] ?? '');

    if ($question && $optionA && $optionB && $optionC && $optionD && in_array($correct, ['A','B','C','D']) && in_array($domain, $allowedDomains, true)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain) VALUES (:question, :a, :b, :c, :d, :correct, :domain)');
            if (!$stmt) {
                throw new Exception('Prepared statement gagal.');
            }
            $result = $stmt->execute(['question' => $question, 'a' => $optionA, 'b' => $optionB, 'c' => $optionC, 'd' => $optionD, 'correct' => $correct, 'domain' => $domain]);
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
        $_SESSION['error'] = 'Semua field wajib diisi, pilih domain yang valid, dan jawaban benar harus A, B, C, atau D.';
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
            <th>Domain</th>
            <th>Jawaban Benar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($questions as $question): ?>
            <tr>
              <td><?php echo $question['id']; ?></td>
              <td><?php echo htmlspecialchars(substr($question['question'], 0, 70)); ?>...</td>
              <td><?php echo htmlspecialchars($question['domain'] ?? infer_question_domain($question)); ?></td>
              <td><?php echo htmlspecialchars(normalized_correct_answer($question)); ?></td>
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
        <div class="col-md-6">
          <label class="form-label">Pilar Domain</label>
          <select name="domain" class="form-select" required>
            <option value="">Pilih domain</option>
            <option value="Sains dan Lingkungan">Sains dan Lingkungan</option>
            <option value="Geografi dan Sejarah Global">Geografi dan Sejarah Global</option>
            <option value="Teknologi dan Digitalisasi">Teknologi dan Digitalisasi</option>
            <option value="Seni, Budaya, dan Humaniora">Seni, Budaya, dan Humaniora</option>
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
