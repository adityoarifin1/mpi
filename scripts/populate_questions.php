<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo->exec("ALTER TABLE questions ADD COLUMN IF NOT EXISTS domain VARCHAR(64) NOT NULL DEFAULT 'Sains dan Lingkungan'");
} catch (Exception $e) {
}

$questionCount = 1000;
if (PHP_SAPI === 'cli') {
    global $argv;
    if (isset($argv[1]) && is_numeric($argv[1])) {
        $questionCount = max(1, min(5000, (int)$argv[1]));
    }
}

$capitalCities = [
    ['country' => 'Indonesia', 'city' => 'Jakarta'],
    ['country' => 'Malaysia', 'city' => 'Kuala Lumpur'],
    ['country' => 'Singapura', 'city' => 'Singapura'],
    ['country' => 'Amerika Serikat', 'city' => 'Washington D.C.'],
    ['country' => 'Jepang', 'city' => 'Tokyo'],
    ['country' => 'Prancis', 'city' => 'Paris'],
    ['country' => 'Italia', 'city' => 'Roma'],
    ['country' => 'Mesir', 'city' => 'Kairo'],
    ['country' => 'Australia', 'city' => 'Canberra'],
    ['country' => 'Kanada', 'city' => 'Ottawa'],
];

$antonyms = [
    ['word' => 'besar', 'opposite' => 'kecil'],
    ['word' => 'panjang', 'opposite' => 'pendek'],
    ['word' => 'cepat', 'opposite' => 'lambat'],
    ['word' => 'terang', 'opposite' => 'gelap'],
    ['word' => 'tinggi', 'opposite' => 'rendah'],
    ['word' => 'kuat', 'opposite' => 'lemah'],
    ['word' => 'berat', 'opposite' => 'ringan'],
    ['word' => 'panas', 'opposite' => 'dingin'],
    ['word' => 'kaya', 'opposite' => 'miskin'],
    ['word' => 'ramai', 'opposite' => 'sepi'],
];

function shuffleOptions(array $correctAndWrong): array {
    $choices = [];
    $letters = ['A', 'B', 'C', 'D'];
    shuffle($correctAndWrong);
    foreach ($correctAndWrong as $index => $option) {
        $choices[$letters[$index]] = $option;
    }
    return $choices;
}

function buildWrongAnswers($correct, string $type): array {
    if ($type === 'number') {
        $correctValue = is_array($correct) ? (int)$correct[0] : (int)$correct;
        return [
            (string)($correctValue + 1),
            (string)($correctValue - 1),
            (string)($correctValue + 2),
        ];
    }
    if ($type === 'capital') {
        return [$correct . 'a', 'Bandung', 'Medan'];
    }
    if ($type === 'antonym') {
        return ['berani', 'senang', 'aktif'];
    }
    return ['Pilihan 1', 'Pilihan 2', 'Pilihan 3'];
}

function optionLetterForIndex(int $index): string {
    $letters = ['A', 'B', 'C', 'D'];
    return $letters[$index] ?? 'A';
}

$questions = [];
for ($i = 1; $i <= $questionCount; $i++) {
    if ($i <= 400) {
        $a = rand(1, 80);
        $b = rand(1, 80);
        $correctAnswer = (string)($a + $b);
        $wrong = buildWrongAnswers([$correctAnswer], 'number');
        $options = shuffleOptions(array_merge([$correctAnswer], $wrong));
        $correctIndex = array_search($correctAnswer, $options, true);
        $questions[] = [
            'question' => "Berapa hasil dari $a + $b?",
            'option_a' => $options['A'],
            'option_b' => $options['B'],
            'option_c' => $options['C'],
            'option_d' => $options['D'],
            'correct' => optionLetterForIndex((int) $correctIndex),
            'domain' => 'Teknologi dan Digitalisasi',
        ];
        continue;
    }

    if ($i <= 700) {
        $entry = $capitalCities[($i - 401) % count($capitalCities)];
        $correctAnswer = $entry['city'];
        $wrong = buildWrongAnswers($correctAnswer, 'capital');
        $options = shuffleOptions(array_merge([$correctAnswer], $wrong));
        $correctIndex = array_search($correctAnswer, $options, true);
        $questions[] = [
            'question' => "Apa ibukota dari {$entry['country']}?",
            'option_a' => $options['A'],
            'option_b' => $options['B'],
            'option_c' => $options['C'],
            'option_d' => $options['D'],
            'correct' => optionLetterForIndex((int) $correctIndex),
            'domain' => 'Geografi dan Sejarah Global',
        ];
        continue;
    }

    if ($i <= 900) {
        $entry = $antonyms[($i - 701) % count($antonyms)];
        $correctAnswer = $entry['opposite'];
        $wrong = buildWrongAnswers($correctAnswer, 'antonym');
        $options = shuffleOptions(array_merge([$correctAnswer], $wrong));
        $correctIndex = array_search($correctAnswer, $options, true);
        $questions[] = [
            'question' => "Apa lawan kata dari '{$entry['word']}'?",
            'option_a' => $options['A'],
            'option_b' => $options['B'],
            'option_c' => $options['C'],
            'option_d' => $options['D'],
            'correct' => optionLetterForIndex((int) $correctIndex),
            'domain' => 'Seni, Budaya, dan Humaniora',
        ];
        continue;
    }

    $month = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][($i - 901) % 12];
    $correctAnswer = $month;
    $wrong = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $wrong = array_filter($wrong, fn($item) => $item !== $correctAnswer);
    shuffle($wrong);
    $options = shuffleOptions(array_merge([$correctAnswer], array_slice($wrong, 0, 3)));
    $correctIndex = array_search($correctAnswer, $options, true);
    $questions[] = [
        'question' => "Bulan apa yang biasanya memiliki 30 hari selain April?",
        'option_a' => $options['A'],
        'option_b' => $options['B'],
        'option_c' => $options['C'],
        'option_d' => $options['D'],
        'correct' => optionLetterForIndex((int) $correctIndex),
        'domain' => 'Geografi dan Sejarah Global',
    ];
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain) VALUES (:question, :a, :b, :c, :d, :correct, :domain)');
    foreach ($questions as $item) {
        $stmt->execute([
            ':question' => $item['question'],
            ':a' => $item['option_a'],
            ':b' => $item['option_b'],
            ':c' => $item['option_c'],
            ':d' => $item['option_d'],
            ':correct' => $item['correct'],
            ':domain' => $item['domain'],
        ]);
    }
    $pdo->commit();
    $message = "Berhasil menambahkan {$questionCount} soal ke database.";
} catch (Exception $e) {
    $pdo->rollBack();
    $message = 'Gagal menambahkan soal: ' . $e->getMessage();
}

if (PHP_SAPI === 'cli') {
    echo $message . PHP_EOL;
} else {
    echo '<pre>' . htmlspecialchars($message) . '</pre>';
}
