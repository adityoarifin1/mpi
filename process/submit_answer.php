<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

function normalize_correct_answer($question) {
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

function normalize_questions(array $questions): array {
    foreach ($questions as &$question) {
        $question['correct_answer'] = normalize_correct_answer($question);
    }

    return $questions;
}

function classify_domain($question) {
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

    if (preg_match('/ibukota|benua|gunung|negara|presiden|proklamasi|tahun|kota|bulan|sejarah|indonesia|geo|geografi|global|lokasi|wilayah|negara/', $text)) {
        return 'Geografi dan Sejarah Global';
    }

    return 'Sains dan Lingkungan';
}

function build_question_payload($question, $index) {
    $options = [
        'A' => $question['option_a'],
        'B' => $question['option_b'],
        'C' => $question['option_c'],
        'D' => $question['option_d'],
    ];
    return [
        'id' => $question['id'],
        'question' => $question['question'],
        'options' => $options,
        'domain' => classify_domain($question),
        'currentIndex' => $index + 1,
        'total' => count($_SESSION['quiz_questions']),
    ];
}

if ($action === 'start') {
    try {
        $stmt = $pdo->query('SELECT * FROM questions ORDER BY RAND() LIMIT 10');
        $questions = $stmt->fetchAll();
        if (!$questions || empty($questions)) {
            http_response_code(404);
            echo json_encode(['error' => 'Soal tidak tersedia.']);
            exit;
        }
        $_SESSION['quiz_questions'] = normalize_questions($questions);
        $_SESSION['quiz_index'] = 0;
        $_SESSION['quiz_score'] = 0;
        $_SESSION['quiz_correct'] = 0;
        $_SESSION['quiz_wrong'] = 0;
        $_SESSION['quiz_started_at'] = time();
        $_SESSION['quiz_done'] = false;

        http_response_code(200);
        echo json_encode(['question' => build_question_payload($_SESSION['quiz_questions'][0], 0)]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal memulai kuis.']);
        exit;
    }
}

if ($action === 'answer') {
    if (!isset($_SESSION['quiz_questions'], $_SESSION['quiz_index'])) {
        echo json_encode(['error' => 'Quiz belum dimulai.']);
        exit;
    }
    $selected = strtoupper($data['answer'] ?? '');
    $index = $_SESSION['quiz_index'];
    $question = $_SESSION['quiz_questions'][$index];
    $isCorrect = ($selected === strtoupper($question['correct_answer']));

    if ($isCorrect) {
        $_SESSION['quiz_score'] += 10;
        $_SESSION['quiz_correct'] += 1;
    } else {
        $_SESSION['quiz_wrong'] += 1;
    }

    $_SESSION['quiz_index'] += 1;
    $finished = $_SESSION['quiz_index'] >= count($_SESSION['quiz_questions']);

    if ($finished) {
        $_SESSION['quiz_done'] = true;
        echo json_encode([
            'correct' => $isCorrect,
            'score' => $_SESSION['quiz_score'],
            'done' => true,
        ]);
        exit;
    }

    $nextQuestion = $_SESSION['quiz_questions'][$_SESSION['quiz_index']];
    echo json_encode([
        'correct' => $isCorrect,
        'score' => $_SESSION['quiz_score'],
        'done' => false,
        'question' => build_question_payload($nextQuestion, $_SESSION['quiz_index']),
    ]);
    exit;
}

echo json_encode(['error' => 'Aksi tidak valid.']);
