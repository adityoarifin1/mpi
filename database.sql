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
    correct_answer CHAR(1) NOT NULL
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

INSERT INTO users (nama, username, email, password, role) VALUES
('Admin Sistem', 'admin', 'admin@quizapp.local', '$2y$10$oK9768fRhdMaDGTmTkXC8OO44cutXDctc2Zkl2jjWbm9EPbRKv4jy', 'admin');

INSERT INTO questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES
('Apa ibukota Indonesia?', 'Bandung', 'Jakarta', 'Surabaya', 'Medan', 'B'),
('Planet paling dekat dengan Matahari adalah?', 'Merkurius', 'Venus', 'Bumi', 'Mars', 'A'),
('Bahasa resmi Perserikatan Bangsa-Bangsa bukan termasuk?', 'Arab', 'Perancis', 'Portugis', 'Rusia', 'C'),
('Tiga warna primer dalam seni visual adalah?', 'Merah, kuning, biru', 'Hijau, ungu, oranye', 'Hitam, putih, abu-abu', 'Coklat, merah, biru', 'A'),
('Presiden pertama Republik Indonesia adalah?', 'Soekarno', 'Soeharto', 'Habibie', 'Megawati', 'A'),
('Simbol kimia untuk emas adalah?', 'Ag', 'Au', 'Fe', 'Pb', 'B'),
('Tahun Proklamasi Kemerdekaan RI adalah?', '1945', '1950', '1942', '1948', 'A'),
('Gunung tertinggi di dunia adalah?', 'Gunung Everest', 'K2', 'Kangchenjunga', 'Lhotse', 'A'),
('Organ tubuh yang berfungsi memompa darah adalah?', 'Paru-paru', 'Ginjal', 'Jantung', 'Hati', 'C'),
('Benua terkecil di dunia adalah?', 'Australia', 'Eropa', 'Antartika', 'Amerika Selatan', 'A');
