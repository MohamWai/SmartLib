<?php
include "db.php";

$type = $_GET['type'] ?? "";
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: admin.php");
    exit();
}

if ($type === "book") {
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php?msg=deleted");
}
elseif ($type === "user") {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php?msg=user_deleted");
} else {
    header("Location: admin.php");
}
exit();
?>