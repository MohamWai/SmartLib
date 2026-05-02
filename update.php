<?php
include "db.php";

$id = (int) $_POST['id'];
$title = $conn->real_escape_string($_POST['title']);
$author = $conn->real_escape_string($_POST['author']);
$category = $conn->real_escape_string($_POST['category']);
$summary = $conn->real_escape_string($_POST['summary'] ?? '');

$conn->query("UPDATE books SET 
title='$title', 
author='$author', 
category='$category',
summary='$summary'
WHERE book_id=$id");

header("Location: admin.php");
?>