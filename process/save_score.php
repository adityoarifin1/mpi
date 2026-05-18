<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User tidak terautentikasi.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak valid.']);
    exit;
}

$score = (int) ($data['score'] ?? 0);
$correct = (int) ($data['correct_answers'] ?? 0);
$wrong = (int) ($data['wrong_answers'] ?? 0);
$completionTime = sanitize($data['completion_time'] ?? '0:00');

try {
    $stmt = $pdo->prepare('INSERT INTO scores (user_id, score, correct_answers, wrong_answers, completion_time) VALUES (:user_id, :score, :correct_answers, :wrong_answers, :completion_time)');
    if (!$stmt) {
        throw new Exception('Prepared statement gagal.');
    }
    
    $result = $stmt->execute([
        'user_id' => $_SESSION['user']['id'],
        'score' => $score,
        'correct_answers' => $correct,
        'wrong_answers' => $wrong,
        'completion_time' => $completionTime,
    ]);
    
    if (!$result) {
        throw new Exception('Execute query gagal.');
    }

    http_response_code(200);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan skor: ' . $e->getMessage()]);
}
