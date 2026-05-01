<?php
include "db.php";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$firstName = trim($_POST["first_name"] ?? "");
$lastName = trim($_POST["last_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$age = (int) ($_POST["age"] ?? 0);
$password = (string) ($_POST["password"] ?? "");
$passwordConfirm = (string) ($_POST["password_confirm"] ?? "");
$patronType = trim($_POST["patron_type"] ?? "student");

$errors = [];
if ($firstName === "" || $lastName === "") {
    $errors[] = "First and last name are required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email is required.";
}
if ($age < 16 || $age > 100) {
    $errors[] = "Age must be between 16 and 100.";
}
if (strlen($password) < 8 || $password !== $passwordConfirm) {
    $errors[] = "Passwords must match and be at least 8 characters.";
}
if (!in_array($patronType, ["student", "faculty"], true)) {
    $patronType = "student";
}

$saveOk = false;
if (count($errors) === 0) {
    $fullName = $firstName . " " . $lastName;
    $role = ucfirst($patronType);
    $stmt = $conn->prepare("INSERT INTO users (name, age, role, email) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("siss", $fullName, $age, $role, $email);
        $saveOk = $stmt->execute();
        if (!$saveOk) {
            $errors[] = "Could not save user (email might already exist).";
        }
        $stmt->close();
    } else {
        $errors[] = "Could not prepare database statement.";
    }
}

$stats = $conn->query("SELECT role, COUNT(*) AS total FROM users GROUP BY role ORDER BY total DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SmartLib - Registration Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="h3 mb-3">Registration Result</h1>
  <a class="btn btn-secondary btn-sm mb-3" href="signup.html">Back to Sign up</a>

  <?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php elseif ($saveOk): ?>
    <div class="alert alert-success">User was saved successfully.</div>
  <?php endif; ?>

  <h2 class="h5 mt-4">Submitted Data</h2>
  <table class="table table-bordered bg-white">
    <tr><th>First Name</th><td><?= e($firstName) ?></td></tr>
    <tr><th>Last Name</th><td><?= e($lastName) ?></td></tr>
    <tr><th>Email</th><td><?= e($email) ?></td></tr>
    <tr><th>Age</th><td><?= e($age) ?></td></tr>
    <tr><th>Patron Type</th><td><?= e(ucfirst($patronType)) ?></td></tr>
  </table>

  <h2 class="h5 mt-4">Users by Role (from DB)</h2>
  <table class="table table-striped table-bordered bg-white">
    <thead class="table-dark"><tr><th>Role</th><th>Total Users</th></tr></thead>
    <tbody>
    <?php if ($stats && $stats->num_rows > 0): ?>
      <?php while ($row = $stats->fetch_assoc()): ?>
        <tr><td><?= e($row["role"]) ?></td><td><?= e($row["total"]) ?></td></tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="2">No users found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
