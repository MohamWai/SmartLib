<?php
include "db.php";
require_once __DIR__ . "/smartlib_navbar.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: signup.html");
    exit();
}

$errors = [];

$first = isset($_POST["first_name"]) ? trim($_POST["first_name"]) : "";
$last = isset($_POST["last_name"]) ? trim($_POST["last_name"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$passwordConfirm = isset($_POST["password_confirm"]) ? $_POST["password_confirm"] : "";
$patronType = isset($_POST["patron_type"]) ? trim($_POST["patron_type"]) : "";
$ageRaw = isset($_POST["age"]) ? $_POST["age"] : "";

if ($first === "") {
    $errors[] = "First name is required.";
}
if ($last === "") {
    $errors[] = "Last name is required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}
if ($password !== $passwordConfirm) {
    $errors[] = "Passwords do not match.";
}
if ($patronType !== "student" && $patronType !== "faculty") {
    $errors[] = "Please select a patron type.";
}

$age = filter_var($ageRaw, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 120]]);
if ($age === false) {
    $errors[] = "Please enter a valid age (1–120).";
}

if (!empty($errors)) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registration error — SmartLib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-body-secondary d-flex flex-column min-vh-100">
    <?php smartlib_navbar('signup'); ?>
    <div class="container flex-grow-1 py-4 py-md-5" style="max-width: 640px">
        <div class="alert alert-danger shadow-sm">
            <h1 class="h4">Could not register</h1>
            <ul class="mb-3"><?php foreach ($errors as $e) { echo "<li>" . htmlspecialchars($e) . "</li>"; } ?></ul>
            <a href="signup.html" class="btn btn-primary">Back to sign up</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
    exit();
}

$name = $first . " " . $last;
if (strlen($name) > 100) {
    $name = substr($name, 0, 100);
}
$role = $patronType === "faculty" ? "faculty" : "student";

$stmt = $conn->prepare("INSERT INTO users (name, age, role, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $name, $age, $role, $email);

if (!$stmt->execute()) {
    $stmt->close();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registration error — SmartLib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-body-secondary d-flex flex-column min-vh-100">
    <?php smartlib_navbar('signup'); ?>
    <div class="container flex-grow-1 py-4 py-md-5" style="max-width: 640px">
        <div class="alert alert-danger">Registration failed. Please try again.</div>
        <a href="signup.html" class="btn btn-primary">Back to sign up</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
    exit();
}

$newId = (int) $conn->insert_id;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Welcome — SmartLib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-body-secondary d-flex flex-column min-vh-100">
    <?php smartlib_navbar('signup'); ?>
    <div class="container flex-grow-1 py-4" style="max-width: 720px">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h3 text-primary mb-3">Account created</h1>
                <p class="text-secondary mb-4">Your patron record was saved with the details below.</p>
                <table class="table table-bordered table-striped mb-4">
                    <caption class="small text-muted">New patron record</caption>
                    <tbody>
                        <tr><th scope="row">Account ID</th><td><?php echo $newId; ?></td></tr>
                        <tr><th scope="row">First name</th><td><?php echo htmlspecialchars($first); ?></td></tr>
                        <tr><th scope="row">Last name</th><td><?php echo htmlspecialchars($last); ?></td></tr>
                        <tr><th scope="row">Email</th><td><?php echo htmlspecialchars($email); ?></td></tr>
                        <tr><th scope="row">Age</th><td><?php echo (int) $age; ?></td></tr>
                        <tr><th scope="row">Role</th><td><?php echo htmlspecialchars($role); ?></td></tr>
                    </tbody>
                </table>
                <a href="index.html" class="btn btn-primary me-2">Home</a>
                <a href="account.php" class="btn btn-outline-secondary">Account</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
