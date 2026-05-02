<?php
include "db.php";
require_once __DIR__ . "/smartlib_navbar.php";

if (empty($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM books WHERE book_id = $id");
if (!$result || $result->num_rows === 0) {
    header("Location: admin.php");
    exit();
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit book — SmartLib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <?php smartlib_navbar('admin'); ?>

    <div class="container flex-grow-1 my-4" style="max-width: 560px">
        <h1 class="h3 mb-3">Edit book</h1>
        <form action="update.php" method="post" class="card shadow-sm p-4">
            <input type="hidden" name="id" value="<?php echo (int) $row['book_id']; ?>">
            <div class="mb-3">
                <label class="form-label" for="title">Title</label>
                <input class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="author">Author</label>
                <input class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($row['author'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="category">Category</label>
                <input class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($row['category'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="summary">Summary</label>
                <textarea class="form-control" id="summary" name="summary" rows="4"><?php echo htmlspecialchars($row['summary'] ?? ''); ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="admin.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
