<?php
include "db.php";
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
// UPLOAD BOOK
if (isset($_POST['upload_title']) && !isset($_POST['title'])) {
    $title = trim($_POST['upload_title'] ?? "");
    $author = trim($_POST['upload_author'] ?? "");
    $summary = trim($_POST['upload_summary'] ?? "");
    $fileName = "";

    if (isset($_FILES['upload_file']) && is_array($_FILES['upload_file'])) {
        $fileName = basename((string) ($_FILES['upload_file']['name'] ?? ""));
    }

    if ($title !== "" && $author !== "") {
        $stmt = $conn->prepare("INSERT INTO books (title, author, category, price_omr, summary, file_name) VALUES (?, ?, 'Uncategorized', 0.00, ?, ?)");
        $stmt->bind_param("ssss", $title, $author, $summary, $fileName);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin.php?msg=uploaded");
    exit();
}

// MANAGE USERS (Insert / Update)
if (isset($_POST['user_name'])) {
    $name = trim($_POST['user_name'] ?? "");
    $age = (int) ($_POST['user_age'] ?? 0);
    $role = trim($_POST['user_role'] ?? "");
    $email = trim($_POST['user_email'] ?? "");
    $userWriteOk = true;

    // UPDATE
    if (isset($_POST['user_id']) && $name !== "" && $age > 0 && $role !== "" && $email !== "") {
        $uid = (int) $_POST['user_id'];
        $stmt = $conn->prepare("UPDATE users SET name = ?, age = ?, role = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sissi", $name, $age, $role, $email, $uid);
        $userWriteOk = $stmt->execute();
        $stmt->close();
    } else {
        // INSERT
        if ($name !== "" && $age > 0 && $role !== "" && $email !== "") {
            $stmt = $conn->prepare("INSERT INTO users (name, age, role, email) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $name, $age, $role, $email);
            $userWriteOk = $stmt->execute();
            $stmt->close();
        }
    }

    if (!$userWriteOk) {
        header("Location: admin.php?msg=user_error");
    } elseif (isset($_POST['user_id'])) {
        header("Location: admin.php?msg=user_updated");
    } else {
        header("Location: admin.php?msg=user_added");
    }
    exit();
}
$userEditMode=false;
if (isset($_GET['edit_user'])){
  $userEditMode = true;
  $uid = (int) $_GET['edit_user'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->bind_param("i", $uid);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();
  $stmt->close();
}

$editMode = false;

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = (int) $_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();
}

// MANAGE BOOKS (Insert / Update)
if (isset($_POST['title'])) {
    $title = trim($_POST['title'] ?? "");
    $author = trim($_POST['author'] ?? "");
    $price = (float) ($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? "");

    if (isset($_POST['id']) && $title !== "" && $author !== "" && $category !== "") {
        $id = (int) $_POST['id'];
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ?, price_omr = ? WHERE book_id = ?");
        $stmt->bind_param("sssdi", $title, $author, $category, $price, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        if ($title !== "" && $author !== "" && $category !== "") {
            $stmt = $conn->prepare("INSERT INTO books (title, author, category, price_omr) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssd", $title, $author, $category, $price);
            $stmt->execute();
            $stmt->close();
        }
    }

   if (isset($_POST['id'])) {
    header("Location: admin.php?msg=updated");
    } else {
        header("Location: admin.php?msg=added");
    }
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SmartLib - Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-style navbar-dark" style="background-color: rgb(0, 54, 82);">
  <div class="container">
    <a href="index.html" class="navbar-brand">
      <img src="./assets/SmartLib_logo.jpg" width="40" height="30">
      SmartLib
    </a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="search.php" class="nav-link">Search</a></li>
        <li class="nav-item"><a href="reports.html" class="nav-link">Reports</a></li>
        <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
        <li class="nav-item"><a href="contact.html" class="nav-link">Contact Us</a></li>
        <li class="nav-item"><a href="signup.html" class="nav-link">Sign up</a></li>
        <li class="nav-item"><a href="account.html" class="nav-link">Account</a></li>
        <li class="nav-item"><a href="admin.php" class="nav-link active">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">

<h2 class="text-center mb-4 fw-bold">Admin Dashboard</h2>
<div class="text-center mb-3">
  <a href="oop_books.php" class="btn btn-outline-dark btn-sm">Open OOP Books Report</a>
</div>

<?php if (isset($_GET['msg'])): ?>

<?php if ($_GET['msg'] == 'added'): ?>
<div class="alert alert-success text-center">✅ Book added successfully</div>

<?php elseif ($_GET['msg'] == 'updated'): ?>
<div class="alert alert-info text-center">✏️ Book updated successfully</div>

<?php elseif ($_GET['msg'] == 'deleted'): ?>
<div class="alert alert-danger text-center">🗑 Book deleted</div>

<?php elseif ($_GET['msg'] == 'user_added'): ?>
<div class="alert alert-success text-center">👤 User added</div>

<?php elseif ($_GET['msg'] == 'user_deleted'): ?>
<div class="alert alert-danger text-center">🗑 User deleted</div>

<?php elseif ($_GET['msg'] == 'user_updated'): ?>
<div class="alert alert-info text-center">✏️ User updated successfully</div>

<?php elseif ($_GET['msg'] == 'user_error'): ?>
<div class="alert alert-warning text-center">⚠️ Could not save user (email may already exist)</div>

<?php elseif ($_GET['msg'] == 'uploaded'): ?>
<div class="alert alert-success text-center">📚 Book uploaded successfully</div>

<?php endif; ?>

<?php endif; ?>
<script>
setTimeout(() => {
  let alert = document.querySelector('.alert');
  if(alert) alert.style.display = 'none';
}, 3000);
</script>

<!-- Upload Section (UNCHANGED) -->
<div class="card p-4 shadow mb-4">
    <h5 class="mb-3 fw-bold text-center">Add New Book (Upload)</h5>

<form method="POST" enctype="multipart/form-data">
  <input name="upload_title" class="form-control mb-2" placeholder="Book Title">
  <input name="upload_author" class="form-control mb-2" placeholder="Author">
  <textarea name="upload_summary" class="form-control mb-2" placeholder="Summary"></textarea>
  <input id="uFile" name="upload_file" type="file" class="form-control mb-3">

  <button class="btn btn-secondary w-100">
    Upload Book
  </button>
</form>
</div>

<div class="row">

<div class="col-md-6">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-3 fw-bold">Manage Books</h5>

<form method="POST">
    <?php if($editMode): ?>
      <input type="hidden" name="id" value="<?= e($book['book_id']) ?>">
    <?php endif; ?>
    <input name="title" class="form-control mb-2" placeholder="Book Name"
    value="<?= $editMode ? e($book['title']) : '' ?>" required>

    <input name="author" class="form-control mb-2" placeholder="Author"
    value="<?= $editMode ? e($book['author']) : '' ?>" required>

    <input name="price" type="number" class="form-control mb-2" placeholder="Price"
    value="<?= $editMode ? e($book['price_omr']) : '' ?>" required>

    <input name="category" class="form-control mb-2" placeholder="Category"
    value="<?= $editMode ? e($book['category']) : '' ?>" required>
<div class="d-flex gap-2">
  <button class="btn btn-primary w-100">
    <?= $editMode ? 'Update Book' : 'Add Book' ?>
  </button>

  <?php if($editMode): ?>
    <a href="admin.php" class="btn btn-secondary w-100">
      Cancel
    </a>
  <?php endif; ?>
</div>
</form>
<br />
<table class="table table-bordered table-striped text-center">
<thead class="table-dark">
<tr>
<th>Name</th>
<th>Author</th>
<th>Price</th>
<th>Category</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$result = $conn->query("SELECT * FROM books");

while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= e($row['title']) ?></td>
<td><?= e($row['author']) ?></td>
<td><?= e($row['price_omr'] ?? '0') ?></td>
<td><?= e($row['category']) ?></td>
<td>
<a href="admin.php?edit=<?= e($row['book_id']) ?>" class="btn btn-warning btn-sm">
    ✏️ Edit
</a>

<a href="delete.php?type=book&id=<?= e($row['book_id']) ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this book?')">
   🗑 Delete
</a>
</td>
</tr>
<?php } ?>
</tbody>

</table>
</div>
</div>

<!-- USER MANAGEMENT (UNCHANGED) -->
<div class="col-md-6">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-3 fw-bold">Manage Users</h5>

<form method="POST">

<?php if($userEditMode): ?>
<input type="hidden" name="user_id" value="<?= e($user['id']) ?>">
<?php endif; ?>

<input name="user_name" class="form-control mb-2" placeholder="User Name"
value="<?= $userEditMode ? e($user['name']) : '' ?>" required>

<input name="user_age" type="number" class="form-control mb-2" placeholder="Age"
value="<?= $userEditMode ? e($user['age']) : '' ?>" required>

<input name="user_role" class="form-control mb-2" placeholder="Role"
value="<?= $userEditMode ? e($user['role']) : '' ?>" required>

<input name="user_email" type="email" class="form-control mb-2" placeholder="Email"
value="<?= $userEditMode ? e($user['email']) : '' ?>" required>

<div class="d-flex gap-2">
  <button class="btn btn-success w-100">
    <?= $userEditMode ? 'Update User' : 'Add User' ?>
  </button>

  <?php if($userEditMode): ?>
    <a href="admin.php" class="btn btn-secondary w-100">
      Cancel
    </a>
  <?php endif; ?>
</div>

</form>
<br />
<table class="table table-bordered table-striped text-center">
<thead class="table-dark">
<tr>
<th>Name</th>
<th>Age</th>
<th>Role</th>
<th>Email</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$users = $conn->query("SELECT * FROM users");

while($u = $users->fetch_assoc()) {
?>
<tr>
<td><?= e($u['name']) ?></td>
<td><?= e($u['age']) ?></td>
<td><?= e($u['role']) ?></td>
<td><?= e($u['email'] ?? '') ?></td>
<td>
<a href="admin.php?edit_user=<?= e($u['id']) ?>" class="btn btn-warning btn-sm">
    ✏️ Edit
</a>

<a href="delete.php?type=user&id=<?= e($u['id']) ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this user?')">
   🗑 Delete
</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

</div>
</div>

<footer class="bg-primary text-white py-4 mt-auto">
<div class="container text-center">
<p class="small mb-0">© 2026 SmartLib Management</p>
</div>
</footer>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>