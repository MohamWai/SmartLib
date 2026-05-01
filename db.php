<?php

$dbHost = "localhost";
$dbUser = "root";       
$dbPass = "";           
$dbName = "library_db"; 

$conn = new mysqli($dbHost, $dbUser, $dbPass);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die("Database creation failed: " . $conn->error);
}

if (!$conn->select_db($dbName)) {
    die("Database selection failed: " . $conn->error);
}

$isReset = isset($_GET["reset"]) && $_GET["reset"] === "1";
$isInit = isset($_GET["init"]) && $_GET["init"] === "1";

if ($isReset) {
    if (
        !$conn->query("SET FOREIGN_KEY_CHECKS = 0") ||
        !$conn->query("DROP TABLE IF EXISTS borrow") ||
        !$conn->query("DROP TABLE IF EXISTS users") ||
        !$conn->query("DROP TABLE IF EXISTS books") ||
        !$conn->query("SET FOREIGN_KEY_CHECKS = 1")
    ) {
        die("Reset failed: " . $conn->error);
    }
}

$booksSql = "
CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price_omr DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
    summary TEXT,
    file_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";

$usersSql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    role VARCHAR(50) NOT NULL,
    email VARCHAR(150) UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";

$borrowSql = "
CREATE TABLE IF NOT EXISTS borrow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    CONSTRAINT uq_borrow UNIQUE (user_id, book_id, borrow_date),
    CONSTRAINT fk_borrow_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_borrow_book FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";

if (!$conn->query($booksSql)) {
    die("Table creation failed (books): " . $conn->error);
}

if (!$conn->query($usersSql)) {
    die("Table creation failed (users): " . $conn->error);
}

if (!$conn->query($borrowSql)) {
    die("Table creation failed (borrow): " . $conn->error);
}

$isDirectRequest = realpath($_SERVER["SCRIPT_FILENAME"] ?? "") === __FILE__;
if ($isDirectRequest) {
    if ($isReset) {
        echo "Database reset complete with fresh schema: " . htmlspecialchars($dbName, ENT_QUOTES, "UTF-8");
    } elseif ($isInit) {
        echo "Database connection and schema initialization complete: " . htmlspecialchars($dbName, ENT_QUOTES, "UTF-8");
    } else {
        echo "Database and tables are ready: " . htmlspecialchars($dbName, ENT_QUOTES, "UTF-8");
    }
}
?>