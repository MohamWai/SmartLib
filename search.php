<?php
include "db.php";
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$title = trim($_GET['title'] ?? "");
$category = trim($_GET['category'] ?? "");
$categories = ["Science", "Technology", "History", "Business", "Literature"];

$query = "
SELECT b.*,
       CASE WHEN EXISTS (
           SELECT 1
           FROM borrow br
           WHERE br.book_id = b.book_id
             AND (br.return_date IS NULL OR br.return_date >= CURDATE())
       ) THEN 'Borrowed' ELSE 'Available' END AS status
FROM books b
WHERE 1
";

$types = "";
$params = [];

if ($title !== "") {
    $query .= " AND b.title LIKE ?";
    $types .= "s";
    $params[] = "%" . $title . "%";
}

if ($category !== "" && in_array($category, $categories, true)) {
    $query .= " AND b.category = ?";
    $types .= "s";
    $params[] = $category;
}

$stmt = $conn->prepare($query);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();
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
<nav class="navbar navbar-expand-lg navbar-style navbar-dark" style="background-color: rgb(0, 54, 82);">
      <div class="container">
        <a href="index.html" class="navbar-brand">
          <img
            src="./assets/SmartLib_logo.jpg"
            alt="Logo"
            width="40"
            height="30"
            class="d-inline-block align-text-top"
          />
          SmartLib</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto ms-lg-0 me-lg-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a href="index.html" class="nav-link ">Home</a>
            </li>
            <li class="nav-item">
              <a href="search.php" class="nav-link active">Search</a>
            </li>
            <li class="nav-item">
              <a href="reports.html" class="nav-link">Reports</a>
            </li>
            <li class="nav-item">
              <a href="about.html" class="nav-link ">About</a>
            </li>
            <li class="nav-item">
              <a href="contact.html" class="nav-link">Contact Us</a>
            </li>
            <li class="nav-item">
              <a href="signup.html" class="nav-link">Sign up</a>
            </li>
            <li class="nav-item">
              <a href="account.html" class="nav-link">Account</a>
            </li>
            <li class="nav-item">
              <a href="admin.php" class="nav-link">Admin</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

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
        value="<?= e($_GET['title'] ?? '') ?>"
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

    <button class="btn btn-primary w-100">Search Books</button>

</form>

</div>
</div>

<hr>

<h2 class="text-center mb-4">Search Results</h2>
<div class="alert alert-info text-center fw-bold">
<?php
if (!empty($title) && !empty($category)) {
    echo "Search results for: <strong>" . e($title) . "</strong> in <strong>" . e($category) . "</strong>";
} elseif (!empty($title)) {
    echo "Search results for: <strong>" . e($title) . "</strong>";
} elseif (!empty($category)) {
    echo "Showing category: <strong>" . e($category) . "</strong>";
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
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php if ($results && $results->num_rows > 0): ?>

    <?php while($row = $results->fetch_assoc()): ?>
    <tr>
        <td><?= e($row['title']) ?></td>
        <td><?= e($row['author']) ?></td>
        <td><?= e($row['category']) ?></td>
        <td><?= e($row['status']) ?></td>
    </tr>

    <tr>
        <td colspan="4">
            <?= e($row['summary'] ?? 'No description available') ?>
        </td>
    </tr>
    <?php endwhile; ?>

<?php else: ?>

    <tr>
        <td colspan="4" class="text-center text-danger">
            No books found
        </td>
    </tr>

<?php endif; ?>

</tbody>
</table>
</div>



<!-- FOOTER -->
    <footer class="bg-primary text-white py-4 mt-auto">
      <div class="container text-center">
        <p class="small mb-0">
          <a href="about.html" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">About</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          <a href="contact.html" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Contact</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          © 2026 SmartLib Management. All rights reserved.
        </p>
      </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $stmt->close(); ?>
