-- Database schema for Quiz App
CREATE DATABASE IF NOT EXISTS quiz_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quiz_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT 'assets/img/default-avatar.svg',
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    domain VARCHAR(64) NOT NULL DEFAULT 'Sains dan Lingkungan'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    correct_answers INT NOT NULL,
    wrong_answers INT NOT NULL,
    completion_time VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (nama, username, email, password, role)
VALUES ('Admin Sistem', 'admin', 'admin@quizapp.local', '$2y$10$8UEK0aysPagdCbHz2bztvO3YIppLjpwDKyr4DTvaAH3t4UzAEIrIu', 'admin')
ON DUPLICATE KEY UPDATE
    nama = VALUES(nama),
    email = VALUES(email),
    password = VALUES(password),
    role = VALUES(role);

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Apa ibukota Indonesia?', 'Bandung', 'Jakarta', 'Surabaya', 'Medan', 'B', 'Geografi dan Sejarah Global'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Apa ibukota Indonesia?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Planet paling dekat dengan Matahari adalah?', 'Merkurius', 'Venus', 'Bumi', 'Mars', 'A', 'Sains dan Lingkungan'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Planet paling dekat dengan Matahari adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Bahasa resmi Perserikatan Bangsa-Bangsa bukan termasuk?', 'Arab', 'Perancis', 'Portugis', 'Rusia', 'C', 'Seni, Budaya, dan Humaniora'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Bahasa resmi Perserikatan Bangsa-Bangsa bukan termasuk?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Tiga warna primer dalam seni visual adalah?', 'Merah, kuning, biru', 'Hijau, ungu, oranye', 'Hitam, putih, abu-abu', 'Coklat, merah, biru', 'A', 'Seni, Budaya, dan Humaniora'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Tiga warna primer dalam seni visual adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Presiden pertama Republik Indonesia adalah?', 'Soekarno', 'Soeharto', 'Habibie', 'Megawati', 'A', 'Geografi dan Sejarah Global'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Presiden pertama Republik Indonesia adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Simbol kimia untuk emas adalah?', 'Ag', 'Au', 'Fe', 'Pb', 'B', 'Sains dan Lingkungan'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Simbol kimia untuk emas adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Tahun Proklamasi Kemerdekaan RI adalah?', '1945', '1950', '1942', '1948', 'A', 'Geografi dan Sejarah Global'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Tahun Proklamasi Kemerdekaan RI adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Gunung tertinggi di dunia adalah?', 'Gunung Everest', 'K2', 'Kangchenjunga', 'Lhotse', 'A', 'Geografi dan Sejarah Global'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Gunung tertinggi di dunia adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Organ tubuh yang berfungsi memompa darah adalah?', 'Paru-paru', 'Ginjal', 'Jantung', 'Hati', 'C', 'Sains dan Lingkungan'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Organ tubuh yang berfungsi memompa darah adalah?');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer, domain)
SELECT 'Benua terkecil di dunia adalah?', 'Australia', 'Eropa', 'Antartika', 'Amerika Selatan', 'A', 'Geografi dan Sejarah Global'
WHERE NOT EXISTS (SELECT 1 FROM questions WHERE question = 'Benua terkecil di dunia adalah?');
