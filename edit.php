<?php
include "db.php";

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM books WHERE book_id = $id");
$row = $result->fetch_assoc();
?>

<form action="update.php" method="post">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<input name="title" value="<?php echo $row['title']; ?>">
<input name="author" value="<?php echo $row['author']; ?>">
<input name="category" value="<?php echo $row['category']; ?>">
<input name="price" value="<?php echo $row['price']; ?>">

<button>Update</button>
</form>