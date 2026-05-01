<?php
include "db.php";

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$title = trim($_POST['title'] ?? "");
$author = trim($_POST['author'] ?? "");
$category = trim($_POST['category'] ?? "");
$price = (float) ($_POST['price'] ?? 0);

if ($id > 0 && $title !== "" && $author !== "" && $category !== "") {
    $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ?, price_omr = ? WHERE book_id = ?");
    $stmt->bind_param("sssdi", $title, $author, $category, $price, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin.php");
?>