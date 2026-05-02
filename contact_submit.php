<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.php");
    exit();
}

$fn = trim($_POST["first_name"] ?? "");
$ln = trim($_POST["last_name"] ?? "");
$em = trim($_POST["email"] ?? "");
$msg = trim($_POST["message"] ?? "");

if ($fn === "" || $ln === "" || $em === "" || $msg === "" || !filter_var($em, FILTER_VALIDATE_EMAIL)) {
    header("Location: contact.php?err=1");
    exit();
}

$st = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, message) VALUES (?,?,?,?)");
$st->bind_param("ssss", $fn, $ln, $em, $msg);
if (!$st->execute()) {
    $st->close();
    header("Location: contact.php?err=1");
    exit();
}
$st->close();
header("Location: contact.php?sent=1");
exit();
