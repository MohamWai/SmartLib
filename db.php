<?php
$conn = new mysqli("localhost", "root", "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS smartlib");

// Select database
$conn->select_db("smartlib");

$booksFlag = __DIR__ . "/smartlib_books_no_price.flag";
if (!is_file($booksFlag)) {
    $conn->query("DROP TABLE IF EXISTS borrow");
    $conn->query("DROP TABLE IF EXISTS books");
    file_put_contents($booksFlag, date("c"));
}

$conn->query("
CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    summary TEXT,
    file_name VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");


$schemaFlag = __DIR__ . "/smartlib_users_minimal.flag";
if (!is_file($schemaFlag)) {
    $conn->query("DROP TABLE IF EXISTS borrow");
    $conn->query("DROP TABLE IF EXISTS users");
    file_put_contents($schemaFlag, date("c"));
}

$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NULL,
    role VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");


$conn->query("
CREATE TABLE IF NOT EXISTS borrow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    return_date DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");


$legacyDate = $conn->query("SHOW COLUMNS FROM borrow LIKE 'date'");
if ($legacyDate && $legacyDate->num_rows > 0) {
    $conn->query("ALTER TABLE borrow CHANGE COLUMN `date` `borrow_date` DATE NOT NULL");
}
$retCol = $conn->query("SHOW COLUMNS FROM borrow LIKE 'return_date'");
if (!$retCol || $retCol->num_rows === 0) {
    $conn->query("ALTER TABLE borrow ADD COLUMN return_date DATE NULL AFTER borrow_date");
    $conn->query("UPDATE borrow SET return_date = DATE_ADD(borrow_date, INTERVAL 14 DAY) WHERE return_date IS NULL");
    $conn->query("ALTER TABLE borrow MODIFY return_date DATE NOT NULL");
}


$conn->query("
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS questionnaire_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    respondent_name VARCHAR(150) NOT NULL,
    contact_email VARCHAR(150) NOT NULL,
    visit_frequency VARCHAR(50) DEFAULT NULL,
    would_recommend VARCHAR(20) DEFAULT NULL,
    services_used VARCHAR(500) DEFAULT NULL,
    satisfaction_score INT DEFAULT NULL,
    last_visit_date DATE DEFAULT NULL,
    detailed_feedback TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

// Sample rows: import via seed.sql in phpMyAdmin (not loaded from PHP).
?>