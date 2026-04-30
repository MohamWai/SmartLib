<?php
include "db.php";

$results = [];

$query = "SELECT * FROM books WHERE 1";

if (!empty($_GET['title'])) {
    $title = $_GET['title'];
    $query .= " AND title LIKE '%$title%'";
}

if (!empty($_GET['category'])) {
    $category = $_GET['category'];
    $query .= " AND category='$category'";
}

$results = $conn->query($query);
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
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php if ($results && $results->num_rows > 0): ?>

    <?php while($row = $results->fetch_assoc()): ?>
    <tr>
        <td><?= $row['title'] ?></td>
        <td><?= $row['author'] ?></td>
        <td><?= $row['category'] ?></td>
        <td><?= rand(0,1) ? 'Available' : 'Borrowed' ?></td>
    </tr>

    <tr>
        <td colspan="4">
            <?= $row['summary'] ?? 'No description available' ?>
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
