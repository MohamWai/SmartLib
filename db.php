<?php
$conn = new mysqli("localhost", "root", "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS smartlib");

// Select database
$conn->select_db("smartlib");

// =====================
// BOOKS TABLE
// =====================
$conn->query("
CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    author VARCHAR(100),
    category VARCHAR(50),
    price_omr DECIMAL(6,2),
    summary TEXT,
    file_name VARCHAR(255)
)
");

// =====================
// USERS TABLE
// =====================
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    age INT,
    role VARCHAR(50)
)
");

// =====================
// BORROW TABLE 
// =====================
$conn->query("
CREATE TABLE IF NOT EXISTS borrow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    date DATE
)
");
?>