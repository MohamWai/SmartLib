<?php
include "db.php";
require_once __DIR__ . "/smartlib_navbar.php";
// UPLOAD BOOK — books: title, author, category, summary, file_name (optional)
if (isset($_POST['upload_title']) && !isset($_POST['title'])) {

    $title = trim($_POST['upload_title']);
    $author = trim($_POST['upload_author']);
    $category = trim($_POST['upload_category'] ?? '');
    $summary = trim($_POST['upload_summary'] ?? '');

    if ($category === '') {
        header("Location: admin.php?msg=bad_book");
        exit();
    }

    $file_name = null;
    if (!empty($_FILES['upload_file']['tmp_name']) && is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
        $file_name = basename($_FILES['upload_file']['name']);
        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $target = $uploadDir . '/' . $file_name;
        if (!move_uploaded_file($_FILES['upload_file']['tmp_name'], $target)) {
            $file_name = null;
        }
    }

    if ($file_name !== null) {
        $st = $conn->prepare("INSERT INTO books (title, author, category, summary, file_name) VALUES (?, ?, ?, ?, ?)");
        $st->bind_param("sssss", $title, $author, $category, $summary, $file_name);
    } else {
        $st = $conn->prepare("INSERT INTO books (title, author, category, summary) VALUES (?, ?, ?, ?)");
        $st->bind_param("ssss", $title, $author, $category, $summary);
    }
    $st->execute();
    $st->close();

    header("Location: admin.php?msg=uploaded");
    exit();
}

// MANAGE USERS (Insert / Update) — users table: id, name, age, role, email
if (isset($_POST["user_name"])) {

    $name = trim($_POST["user_name"]);
    $ageRaw = trim($_POST["user_age"] ?? "");
    $age = $ageRaw === "" ? null : (int) $ageRaw;
    $role = trim($_POST["user_role"]);
    $email = trim($_POST["user_email"] ?? "");

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: admin.php?msg=bad_email");
        exit();
    }

    if (isset($_POST["user_id"])) {
        $uid = (int) $_POST["user_id"];
        if ($age === null) {
            $st = $conn->prepare("UPDATE users SET name = ?, age = NULL, role = ?, email = ? WHERE id = ?");
            $st->bind_param("sssi", $name, $role, $email, $uid);
        } else {
            $st = $conn->prepare("UPDATE users SET name = ?, age = ?, role = ?, email = ? WHERE id = ?");
            $st->bind_param("sissi", $name, $age, $role, $email, $uid);
        }
        $st->execute();
        $st->close();
        header("Location: admin.php?msg=user_updated");
    } else {
        if ($age === null) {
            $st = $conn->prepare("INSERT INTO users (name, age, role, email) VALUES (?, NULL, ?, ?)");
            $st->bind_param("sss", $name, $role, $email);
        } else {
            $st = $conn->prepare("INSERT INTO users (name, age, role, email) VALUES (?, ?, ?, ?)");
            $st->bind_param("siss", $name, $age, $role, $email);
        }
        $st->execute();
        $st->close();
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

    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $category = $conn->real_escape_string($_POST['category']);
    $summary = $conn->real_escape_string($_POST['summary'] ?? '');

    if (isset($_POST['id'])) {
        $id = (int) $_POST['id'];

        $conn->query("UPDATE books SET
        title='$title',
        author='$author',
        category='$category',
        summary='$summary'
        WHERE book_id=$id");

    } else {
        $conn->query("INSERT INTO books (title, author, category, summary)
        VALUES ('$title','$author','$category','$summary')");
    }

   if (isset($_POST['id'])) {
    header("Location: admin.php?msg=updated");
    } else {
        header("Location: admin.php?msg=added");
    }
    exit();
}

if (isset($_POST["borrow_user_id"], $_POST["borrow_book_id"])) {
    $buid = (int) $_POST["borrow_user_id"];
    $bbid = (int) $_POST["borrow_book_id"];
    $bdate = trim($_POST["borrow_date"] ?? "");
    $rdate = trim($_POST["return_date"] ?? "");
    if ($bdate === "" || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $bdate)) {
        $bdate = date("Y-m-d");
    }
    if ($rdate === "" || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $rdate)) {
        $rdate = date("Y-m-d", strtotime($bdate . " +14 days"));
    }
    if ($buid > 0 && $bbid > 0) {
        $st = $conn->prepare("INSERT INTO borrow (user_id, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?)");
        $st->bind_param("iiss", $buid, $bbid, $bdate, $rdate);
        $st->execute();
        $st->close();
    }
    header("Location: admin.php?msg=borrowed");
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

<?php smartlib_navbar('admin'); ?>

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

<?php elseif ($_GET['msg'] == 'borrowed'): ?>
<div class="alert alert-success text-center">📖 Borrow record added</div>

<?php elseif ($_GET['msg'] == 'bad_email'): ?>
<div class="alert alert-warning text-center">Enter a valid email for the patron.</div>

<?php elseif ($_GET['msg'] == 'bad_book'): ?>
<div class="alert alert-warning text-center">Category is required for books (matches the <code>books</code> table).</div>

<?php endif; ?>

<?php endif; ?>
<script>
setTimeout(() => {
  let alert = document.querySelector('.alert');
  if(alert) alert.style.display = 'none';
}, 3000);
</script>

<div class="card p-4 shadow mb-4">
    <h5 class="mb-2 fw-bold text-center">Add book (upload file)</h5>
    <p class="small text-muted text-center mb-3">Columns: <code>title</code>, <code>author</code>, <code>category</code>, <code>summary</code>, optional <code>file_name</code></p>

<form method="POST" enctype="multipart/form-data">
  <input name="upload_title" class="form-control mb-2" placeholder="title" required>
  <input name="upload_author" class="form-control mb-2" placeholder="author" required>
  <input name="upload_category" class="form-control mb-2" placeholder="category" required>
  <textarea name="upload_summary" class="form-control mb-2" placeholder="summary (optional)"></textarea>
  <input name="upload_file" type="file" class="form-control mb-3" aria-label="Optional file — stored as file_name">

  <button type="submit" class="btn btn-secondary w-100">
    Upload Book
  </button>
</form>
</div>

<div class="row">

<div class="col-md-6">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-2 fw-bold">Manage books</h5>
<p class="small text-muted text-center mb-3">Same table: <code>title</code>, <code>author</code>, <code>category</code>, <code>summary</code></p>

<form method="POST">
    <?php if($editMode): ?>
      <input type="hidden" name="id" value="<?= $book['book_id'] ?>">
    <?php endif; ?>
    <input name="title" class="form-control mb-2" placeholder="title"
    value="<?= $editMode ? htmlspecialchars($book['title']) : '' ?>" required>

    <input name="author" class="form-control mb-2" placeholder="author"
    value="<?= $editMode ? htmlspecialchars($book['author']) : '' ?>" required>

    <input name="category" class="form-control mb-2" placeholder="category"
    value="<?= $editMode ? htmlspecialchars($book['category']) : '' ?>" required>

    <textarea name="summary" class="form-control mb-2" rows="3" placeholder="summary"><?= $editMode ? htmlspecialchars($book['summary'] ?? '') : '' ?></textarea>
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
<th>title</th>
<th>author</th>
<th>category</th>
<th>summary</th>
<th>file_name</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$result = $conn->query("SELECT * FROM books");

while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= htmlspecialchars($row['title']) ?></td>
<td><?= htmlspecialchars($row['author']) ?></td>
<td><?= htmlspecialchars($row['category']) ?></td>
<td class="small text-start"><?php
$s = $row['summary'] ?? '';
echo htmlspecialchars(mb_strlen($s) > 60 ? mb_substr($s, 0, 60) . '…' : $s);
?></td>
<td class="small"><?= htmlspecialchars($row['file_name'] ?? '—') ?></td>
<td>
<a href="admin.php?edit=<?= $row['book_id'] ?>" class="btn btn-warning btn-sm">
    Edit
</a>

<a href="delete.php?type=book&id=<?= $row['book_id'] ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this book?')">
   Delete
</a>
</td>
</tr>
<?php } ?>
</tbody>

</table>
</div>
</div>

<div class="col-md-6">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-2 fw-bold">Manage users</h5>
<p class="small text-muted text-center mb-3">Table <code>users</code>: <code>name</code>, <code>age</code> (optional), <code>role</code>, <code>email</code></p>

<form method="POST">

<?php if($userEditMode): ?>
<input type="hidden" name="user_id" value="<?= $user['id'] ?>">
<?php endif; ?>

<input name="user_name" class="form-control mb-2" placeholder="name"
value="<?= $userEditMode ? htmlspecialchars($user['name']) : '' ?>" required>

<input name="user_age" type="number" class="form-control mb-2" placeholder="age (optional, NULL in DB)"
value="<?= $userEditMode && $user['age'] !== null && $user['age'] !== '' ? (int) $user['age'] : '' ?>">

<input name="user_role" class="form-control mb-2" placeholder="role"
value="<?= $userEditMode ? htmlspecialchars($user['role']) : '' ?>" required>

<input name="user_email" type="email" class="form-control mb-2" placeholder="email"
value="<?= $userEditMode ? htmlspecialchars($user['email'] ?? '') : '' ?>" required>

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
<th>id</th>
<th>name</th>
<th>age</th>
<th>role</th>
<th>email</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$users = $conn->query("SELECT * FROM users");

while($u = $users->fetch_assoc()) {
?>
<tr>
<td><?= (int) $u['id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= $u['age'] !== null && $u['age'] !== '' ? htmlspecialchars((string) $u['age']) : '<span class="text-muted">NULL</span>' ?></td>
<td><?= htmlspecialchars($u['role']) ?></td>
<td><?= htmlspecialchars($u['email'] ?? '') ?></td>
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

<div class="row mt-4">
<div class="col-12">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-2 fw-bold">Borrow records</h5>
<p class="small text-muted text-center mb-3">Table <code>borrow</code>: <code>user_id</code>, <code>book_id</code>, <code>borrow_date</code>, <code>return_date</code> (due / return-by)</p>
<form method="POST" class="row g-2 align-items-end justify-content-center mb-3 p-3 bg-light rounded border">
  <div class="col-auto">
    <label class="form-label small mb-0" for="borrow_user_id">user_id</label>
    <input id="borrow_user_id" type="number" name="borrow_user_id" class="form-control form-control-sm" min="1" placeholder="users.id" required>
  </div>
  <div class="col-auto">
    <label class="form-label small mb-0" for="borrow_book_id">book_id</label>
    <input id="borrow_book_id" type="number" name="borrow_book_id" class="form-control form-control-sm" min="1" placeholder="books.book_id" required>
  </div>
  <div class="col-auto">
    <label class="form-label small mb-0" for="borrow_date">borrow date</label>
    <input id="borrow_date" type="date" name="borrow_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
  </div>
  <div class="col-auto">
    <label class="form-label small mb-0" for="return_date">return due</label>
    <input id="return_date" type="date" name="return_date" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-sm btn-primary">Add borrow</button>
  </div>
</form>
<div class="table-responsive">
<table class="table table-bordered table-striped text-center table-sm">
<thead class="table-dark">
<tr><th>id</th><th>user (name)</th><th>book (title)</th><th>borrow</th><th>return due</th></tr>
</thead>
<tbody>
<?php
$br = $conn->query(
    "SELECT br.id, u.name AS patron, b.title, br.borrow_date, br.return_date
     FROM borrow br
     JOIN users u ON br.user_id = u.id
     JOIN books b ON br.book_id = b.book_id
     ORDER BY br.borrow_date DESC, br.id DESC
     LIMIT 50"
);
if ($br && $br->num_rows > 0) {
    while ($x = $br->fetch_assoc()) {
        echo "<tr><td>" . (int) $x["id"] . "</td><td>" . htmlspecialchars($x["patron"]) . "</td>";
        echo "<td>" . htmlspecialchars($x["title"]) . "</td><td>" . htmlspecialchars($x["borrow_date"]) . "</td>";
        echo "<td>" . htmlspecialchars($x["return_date"]) . "</td></tr>";
    }
} else {
    echo '<tr><td colspan="5" class="text-muted">No borrow rows yet.</td></tr>';
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>

<div class="row">
<div class="col-md-6">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-3 fw-bold">Recent contact messages</h5>
<div class="table-responsive" style="max-height:280px;overflow:auto">
<table class="table table-sm table-bordered">
<thead class="table-secondary"><tr><th>When</th><th>From</th><th>Preview</th></tr></thead>
<tbody>
<?php
$cm = $conn->query("SELECT created_at, first_name, last_name, email, message FROM contact_messages ORDER BY id DESC LIMIT 15");
if ($cm && $cm->num_rows > 0) {
    while ($x = $cm->fetch_assoc()) {
        $prev = mb_substr($x["message"], 0, 48) . (strlen($x["message"]) > 48 ? "…" : "");
        echo "<tr><td class=\"small\">" . htmlspecialchars($x["created_at"]) . "</td>";
        echo "<td>" . htmlspecialchars($x["first_name"] . " " . $x["last_name"]) . "<br><span class=\"small text-muted\">" . htmlspecialchars($x["email"]) . "</span></td>";
        echo "<td class=\"small\">" . htmlspecialchars($prev) . "</td></tr>";
    }
} else {
    echo '<tr><td colspan="3" class="text-muted text-center">No messages yet.</td></tr>';
}
?>
</tbody>
</table>
</div>
</div>
</div>
<div class="col-md-6">
<div class="card p-4 shadow mb-4">
<h5 class="text-center mb-3 fw-bold">Recent questionnaire responses</h5>
<div class="table-responsive" style="max-height:280px;overflow:auto">
<table class="table table-sm table-bordered">
<thead class="table-secondary"><tr><th>When</th><th>Name</th><th>Score</th></tr></thead>
<tbody>
<?php
$qr = $conn->query(
    "SELECT created_at, respondent_name, satisfaction_score, detailed_feedback FROM questionnaire_responses ORDER BY id DESC LIMIT 15"
);
if ($qr && $qr->num_rows > 0) {
    while ($x = $qr->fetch_assoc()) {
        $df = $x["detailed_feedback"] ?? "";
        $dfs = mb_strlen($df) > 40 ? mb_substr($df, 0, 40) . "…" : $df;
        echo "<tr><td class=\"small\">" . htmlspecialchars($x["created_at"]) . "</td>";
        echo "<td>" . htmlspecialchars($x["respondent_name"]) . "<br><span class=\"small text-muted\">" . htmlspecialchars($dfs) . "</span></td>";
        echo "<td>" . htmlspecialchars((string) ($x["satisfaction_score"] ?? "—")) . "</td></tr>";
    }
} else {
    echo '<tr><td colspan="3" class="text-muted text-center">No responses yet.</td></tr>';
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>

</div>

<footer class="bg-primary text-white py-4 mt-auto">
<div class="container text-center">
<p class="small mb-0">© 2026 SmartLib Management</p>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>

</body>
</html>