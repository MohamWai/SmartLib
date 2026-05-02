<?php
include "db.php";
require_once __DIR__ . "/smartlib_navbar.php";

$monthly = [];
$q = $conn->query(
    "SELECT DATE_FORMAT(borrow_date, '%Y-%m') AS ym,
            DATE_FORMAT(borrow_date, '%M %Y') AS label,
            COUNT(*) AS borrow_cnt,
            COUNT(DISTINCT user_id) AS active_users
     FROM borrow
     GROUP BY ym
     ORDER BY ym DESC
     LIMIT 12"
);
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $ym = $row["ym"];
        $catRow = $conn->query(
            "SELECT b.category, COUNT(*) AS c
             FROM borrow br
             JOIN books b ON br.book_id = b.book_id
             WHERE DATE_FORMAT(br.borrow_date, '%Y-%m') = '" . $conn->real_escape_string($ym) . "'
             GROUP BY b.category
             ORDER BY c DESC
             LIMIT 1"
        );
        $topCat = $catRow && $catRow->num_rows ? $catRow->fetch_assoc()["category"] : "—";
        $row["top_category"] = $topCat;
        $monthly[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartLib - Reports &amp; Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php smartlib_navbar('reports'); ?>

<div class="container my-4">
<h1 class="h3 mb-3">SmartLib Library Reports</h1>
<hr>

<h2 class="text-center mt-4">Library Membership Rules</h2>
<p class="text-center text-muted mb-3">
This table describes borrowing limits and fine policies for different SmartLib members.
</p>

<div class="table-responsive">
<table class="table table-bordered table-striped text-center">
<thead class="table-dark">
<tr>
    <th>Member Type</th>
    <th>Borrow Limit</th>
    <th>Loan Period</th>
    <th>Fine Rate</th>
</tr>
</thead>
<tbody>
<tr>
    <td>Student</td>
    <td>5 Books</td>
    <td>14 Days</td>
    <td rowspan="2">0.5 OMR per day</td>
</tr>
<tr>
    <td>Staff</td>
    <td>10 Books</td>
    <td>30 Days</td>
</tr>
</tbody>
</table>
</div>

<h2 class="text-center mt-5">Borrowing activity (from database)</h2>
<p class="text-center text-muted mb-3">
Totals are computed from the <code>borrow</code> table. Add loans via Admin or seed data.
</p>

<div class="table-responsive">
<table class="table table-bordered table-striped text-center">
<thead class="table-dark">
<tr>
    <th>Month</th>
    <th>Total books borrowed</th>
    <th>Most borrowed category</th>
    <th>Active borrowers</th>
</tr>
</thead>
<tbody>
<?php if (count($monthly) === 0): ?>
<tr><td colspan="4" class="text-muted">No borrowing records yet. Open the site once to run DB setup, or add borrows from Admin.</td></tr>
<?php else: ?>
<?php foreach ($monthly as $m): ?>
<tr>
    <td><?php echo htmlspecialchars($m["label"]); ?></td>
    <td><?php echo (int) $m["borrow_cnt"]; ?></td>
    <td><?php echo htmlspecialchars($m["top_category"]); ?></td>
    <td><?php echo (int) $m["active_users"]; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>

<p class="small text-center text-muted mt-3">
    Total books in catalog: <?php echo (int) $conn->query("SELECT COUNT(*) AS c FROM books")->fetch_assoc()["c"]; ?> ·
    Registered patrons: <?php echo (int) $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()["c"]; ?>
</p>
</div>

<footer class="bg-primary text-white py-4 mt-auto">
      <div class="container text-center">
        <p class="small mb-0">
          <a href="about.html" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">About</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          <a href="contact.php" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Contact</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          © 2026 SmartLib Management. All rights reserved.
        </p>
      </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
