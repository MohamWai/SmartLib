<?php
include "db.php";
require_once __DIR__ . "/book_catalog.php";
require_once __DIR__ . "/smartlib_navbar.php";

$query = "SELECT * FROM books WHERE 1";

if (!empty($_GET['title'])) {
    $t = $conn->real_escape_string($_GET['title']);
    $query .= " AND title LIKE '%{$t}%'";
}

if (!empty($_GET['category'])) {
    $c = $conn->real_escape_string($_GET['category']);
    $query .= " AND category='{$c}'";
}

/** @var Book[] $bookList */
$bookList = smartlib_fetch_books_sql($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartLib - Book Search</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<?php smartlib_navbar('search'); ?>

<!-- CONTENT -->
<div class="container my-4">

<h1 class="h3 mb-3">SmartLib Digital Library</h1>
<hr>

<h2 class="text-center mb-4">Advanced Book Search</h2>

<!-- FORM -->
<div class="row justify-content-center">
<div class="col-md-6">

<form class="p-4 border rounded bg-white" method="GET">

    <div class="mb-3">
      <label class="form-label">Book Title</label>
        <input type="text" name="title" class="form-control"
        value="<?= $_GET['title'] ?? '' ?>"
        placeholder="Enter book title">
    </div>

    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            <option <?= (($_GET['category'] ?? '')=='Science')?'selected':'' ?>>Science</option>
            <option <?= (($_GET['category'] ?? '')=='Technology')?'selected':'' ?>>Technology</option>
            <option <?= (($_GET['category'] ?? '')=='History')?'selected':'' ?>>History</option>
            <option <?= (($_GET['category'] ?? '')=='Business')?'selected':'' ?>>Business</option>
            <option <?= (($_GET['category'] ?? '')=='Literature')?'selected':'' ?>>Literature</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Availability</label><br>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="ebook">
            <label class="form-check-label">eBook Only</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="available">
            <label class="form-check-label">Available Now</label>
        </div>
    </div>

    <button class="btn btn-primary w-100">Search Books</button>

</form>

</div>
</div>

<hr>

<h2 class="text-center mb-4">Search Results</h2>
<div class="alert alert-info text-center fw-bold">
<?php
$title = $_GET['title'] ?? '';
$category = $_GET['category'] ?? '';

if (!empty($title) && !empty($category)) {
    echo "Search results for: <strong>" . htmlspecialchars($title) . "</strong> in <strong>" . htmlspecialchars($category) . "</strong>";
} elseif (!empty($title)) {
    echo "Search results for: <strong>" . htmlspecialchars($title) . "</strong>";
} elseif (!empty($category)) {
    echo "Showing category: <strong>" . htmlspecialchars($category) . "</strong>";
} else {
    echo "Showing all books";
}
?>
</div>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
    <th>Title</th>
    <th>Author</th>
    <th>Category</th>
    <th>Format</th>
</tr>
</thead>

<tbody>
<?php smartlib_render_book_search_table($bookList); ?>
</tbody>
</table>
</div>



<!-- FOOTER -->
    <footer class="bg-primary text-white py-4 mt-auto">
      <div class="container text-center">
        <p class="small mb-0">
          <a href="about.html" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">About</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          <a href="contact.php" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Contact</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          © 2026 SmartLib Management. All rights reserved.
        </p>
      </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
