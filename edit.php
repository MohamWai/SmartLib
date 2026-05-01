<?php
include "db.php";
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>

<form action="update.php" method="post">
<input type="hidden" name="id" value="<?= e($row['book_id'] ?? "") ?>">

<input name="title" value="<?= e($row['title'] ?? "") ?>">
<input name="author" value="<?= e($row['author'] ?? "") ?>">
<input name="category" value="<?= e($row['category'] ?? "") ?>">
<input name="price" value="<?= e($row['price_omr'] ?? "0.00") ?>">

<button>Update</button>
</form>