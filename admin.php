<?php
include "db.php";
// UPLOAD BOOK
if (isset($_POST['upload_title']) && !isset($_POST['title'])) {

    $title = $_POST['upload_title'];
    $author = $_POST['upload_author'];
    $summary = $_POST['upload_summary'];

    $conn->query("INSERT INTO books (title, author, summary)
    VALUES ('$title','$author','$summary')");

    header("Location: admin.php?msg=uploaded");
    exit();
}

// MANAGE USERS (Insert / Update)
if (isset($_POST['user_name'])) {

    $name = $_POST['user_name'];
    $age = $_POST['user_age'];
    $role = $_POST['user_role'];

    // UPDATE
    if (isset($_POST['user_id'])) {
        $uid = $_POST['user_id'];

        $conn->query("UPDATE users SET
        name='$name',
        age='$age',
        role='$role'
        WHERE id=$uid");

    } else {
        // INSERT
        $conn->query("INSERT INTO users (name, age, role)
        VALUES ('$name','$age','$role')");
    }

    if (isset($_POST['user_id'])) {
        header("Location: admin.php?msg=user_updated");
    } else {
        header("Location: admin.php?msg=user_added");
    }
    exit();
}
$userEditMode=false;
if (isset($_GET['edit_user'])){
  $userEditMode = true;
  $uid = $_GET['edit_user'];

  $res = $conn->query("SELECT * FROM users WHERE id = $uid");
  $user = $res->fetch_assoc();
}

$editMode = false;

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];

    $result = $conn->query("SELECT * FROM books WHERE book_id = $id");
    $book = $result->fetch_assoc();
}

// MANAGE BOOKS (Insert / Update)
if (isset($_POST['title'])) {

    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $conn->query("UPDATE books SET
        title='$title',
        author='$author',
        category='$category',
        price_omr='$price'
        WHERE book_id=$id");

    } else {
        $conn->query("INSERT INTO books (title, author, category, price_omr)
        VALUES ('$title','$author','$category','$price')");
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
  <input id="uFile" type="file" class="form-control mb-3">

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
      <input type="hidden" name="id" value="<?= $book['book_id'] ?>">
    <?php endif; ?>
    <input name="title" class="form-control mb-2" placeholder="Book Name"
    value="<?= $editMode ? $book['title'] : '' ?>" required>

    <input name="author" class="form-control mb-2" placeholder="Author"
    value="<?= $editMode ? $book['author'] : '' ?>" required>

    <input name="price" type="number" class="form-control mb-2" placeholder="Price"
    value="<?= $editMode ? $book['price_omr'] : '' ?>" required>

    <input name="category" class="form-control mb-2" placeholder="Category"
    value="<?= $editMode ? $book['category'] : '' ?>" required>
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
<td><?= $row['title'] ?></td>
<td><?= $row['author'] ?></td>
<td><?= $row['price_omr'] ?? '0' ?></td>
<td><?= $row['category'] ?></td>
<td>
<a href="admin.php?edit=<?= $row['book_id'] ?>" class="btn btn-warning btn-sm">
    ✏️ Edit
</a>

<a href="delete.php?type=book&id=<?= $row['book_id'] ?>" 
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
<input type="hidden" name="user_id" value="<?= $user['id'] ?>">
<?php endif; ?>

<input name="user_name" class="form-control mb-2" placeholder="User Name"
value="<?= $userEditMode ? $user['name'] : '' ?>" required>

<input name="user_age" type="number" class="form-control mb-2" placeholder="Age"
value="<?= $userEditMode ? $user['age'] : '' ?>" required>

<input name="user_role" class="form-control mb-2" placeholder="Role"
value="<?= $userEditMode ? $user['role'] : '' ?>" required>

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
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$users = $conn->query("SELECT * FROM users");

while($u = $users->fetch_assoc()) {
?>
<tr>
<td><?= $u['name'] ?></td>
<td><?= $u['age'] ?></td>
<td><?= $u['role'] ?></td>
<td>
<a href="admin.php?edit_user=<?= $u['id'] ?>" class="btn btn-warning btn-sm">
    ✏️ Edit
</a>

<a href="delete.php?type=user&id=<?= $u['id'] ?>" 
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