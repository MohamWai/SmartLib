<?php
include "db.php";

$type = $_GET['type'];
$id = $_GET['id'];

if ($type == "book") {
    $conn->query("DELETE FROM books WHERE book_id = $id");
    header("Location: admin.php?msg=deleted");
}
elseif ($type == "user") {
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: admin.php?msg=user_deleted");
}
exit();
?>