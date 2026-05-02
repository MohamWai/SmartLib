<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: questionaire.html");
    exit();
}

$name = trim($_POST["respondent_name"] ?? "");
$email = trim($_POST["contact_email"] ?? "");
$vf = trim($_POST["visit_frequency"] ?? "");
$wr = trim($_POST["would_recommend"] ?? "");
$rawSv = $_POST["services_used"] ?? [];
if (!is_array($rawSv)) {
    $rawSv = $rawSv !== null && $rawSv !== "" ? [$rawSv] : [];
}
$servicesStr = implode(",", array_map("trim", $rawSv));
$score = isset($_POST["satisfaction_score"]) ? (int) $_POST["satisfaction_score"] : null;
if ($score < 1 || $score > 10) {
    $score = null;
}
$lvd = trim($_POST["last_visit_date"] ?? "");
$feedback = trim($_POST["detailed_feedback"] ?? "");

if ($name === "" || $email === "" || $feedback === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: questionaire.html?err=1");
    exit();
}

if ($lvd !== "" && preg_match("/^\d{4}-\d{2}-\d{2}$/", $lvd)) {
    $st = $conn->prepare(
        "INSERT INTO questionnaire_responses (respondent_name, contact_email, visit_frequency, would_recommend, services_used, satisfaction_score, last_visit_date, detailed_feedback)
         VALUES (?,?,?,?,?,?,?,?)"
    );
    $st->bind_param(
        "sssssiss",
        $name,
        $email,
        $vf,
        $wr,
        $servicesStr,
        $score,
        $lvd,
        $feedback
    );
} else {
    $st = $conn->prepare(
        "INSERT INTO questionnaire_responses (respondent_name, contact_email, visit_frequency, would_recommend, services_used, satisfaction_score, detailed_feedback)
         VALUES (?,?,?,?,?,?,?)"
    );
    $st->bind_param(
        "sssssis",
        $name,
        $email,
        $vf,
        $wr,
        $servicesStr,
        $score,
        $feedback
    );
}

if (!$st->execute()) {
    $st->close();
    header("Location: questionaire.html?err=1");
    exit();
}
$st->close();
header("Location: questionaire.html?saved=1");
exit();
