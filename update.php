<?php
include "db.php";

$id = $_POST['id'];
$title = $_POST['title'];
$author = $_POST['author'];
$category = $_POST['category'];
$price = $_POST['price'];

$conn->query("UPDATE books SET 
title='$title', 
author='$author', 
category='$category', 
price_omr='$price'
WHERE book_id=$id");

header("Location: admin.php");
?>