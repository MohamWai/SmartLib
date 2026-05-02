<?php
include "db.php";
require_once __DIR__ . "/smartlib_navbar.php";

$usersList = [];
$uq = $conn->query("SELECT id, name, age, role, email FROM users ORDER BY name ASC, id ASC");
if ($uq) {
    while ($row = $uq->fetch_assoc()) {
        $usersList[] = $row;
    }
}

$userId = isset($_GET["user"]) ? (int) $_GET["user"] : 0;
if ($userId < 1 && !empty($usersList)) {
    $userId = (int) $usersList[0]["id"];
}

$uRes = $userId > 0
    ? $conn->query("SELECT id, name, age, role, email FROM users WHERE id = " . (int) $userId)
    : false;
$user = $uRes && $uRes->num_rows ? $uRes->fetch_assoc() : null;
if (!$user && !empty($usersList)) {
    $user = $usersList[0];
    $userId = (int) $user["id"];
} elseif (!$user) {
    $userId = 0;
}

function smartlib_borrow_row_status(string $returnDueDate): array
{
    $dueTs = strtotime($returnDueDate);
    $todayTs = strtotime("today");
    if ($todayTs > $dueTs) {
        return ["Overdue", "danger"];
    }
    if (($dueTs - $todayTs) <= 3 * 86400) {
        return ["Due soon", "warning"];
    }
    return ["Checked out", "success"];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SmartLib — My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  </head>
  <body class="d-flex flex-column min-vh-100 bg-body-secondary">
    <?php smartlib_navbar('account'); ?>

    <main class="flex-grow-1 py-4 py-md-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
              <div class="card-body p-4 p-md-5">
                <p class="small text-uppercase fw-semibold text-secondary mb-2">Patron dashboard</p>
                <h1 class="display-6 fw-bold text-primary mb-2">My Account</h1>
                <p class="text-secondary mb-0 lead fs-6">
                  Choose a patron below to see their loans and profile. Data comes from <code>users</code>, <code>borrow</code>, and <code>books</code>.
                </p>
              </div>
            </div>
          </div>

          <?php if (!empty($usersList)): ?>
          <div class="col-12">
            <form method="get" action="account.php" class="card shadow-sm border-0 mb-2" id="patron-picker-form">
              <div class="card-body p-3 p-md-4">
                <label class="form-label fw-semibold mb-2" for="patron-select">View account for</label>
                <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
                  <select name="user" id="patron-select" class="form-select flex-grow-1" style="max-width: 36rem" onchange="this.form.submit()" aria-describedby="patron-help">
                    <?php foreach ($usersList as $u): ?>
                      <option value="<?php echo (int) $u["id"]; ?>"<?php echo (int) $u["id"] === $userId ? " selected" : ""; ?>>
                        <?php echo htmlspecialchars($u["name"]); ?> — <?php echo htmlspecialchars($u["role"] ?? ""); ?> (ID <?php echo (int) $u["id"]; ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn btn-primary d-sm-none">Update</button>
                </div>
                <p id="patron-help" class="small text-muted mb-0 mt-2">Selection updates the page. On desktop, changing the menu applies immediately.</p>
              </div>
            </form>
          </div>
          <?php else: ?>
          <div class="col-12">
            <div class="alert alert-warning">No patrons in the database yet. Add users in <a href="admin.php">Admin</a>.</div>
          </div>
          <?php endif; ?>

          <div class="col-12">
            <ul class="nav nav-tabs nav-fill flex-column flex-sm-row" id="accountTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="borrow-tab" data-bs-toggle="tab" data-bs-target="#borrow-pane" type="button" role="tab" aria-controls="borrow-pane" aria-selected="true">Borrowing history</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab" aria-controls="profile-pane" aria-selected="false">Profile</button>
              </li>
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom bg-white p-4 p-md-4 shadow-sm">
              <div class="tab-pane fade show active" id="borrow-pane" role="tabpanel" aria-labelledby="borrow-tab" tabindex="0">
                <h2 class="h5 fw-semibold text-primary mb-3">Borrowing history</h2>
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-bordered align-middle caption-top mb-0">
                    <caption class="text-body fw-semibold text-start py-2">Loans for this patron — return date is the due / return-by date from the database</caption>
                    <thead class="table-dark">
                      <tr>
                        <th scope="col">Book</th>
                        <th scope="col">Author</th>
                        <th scope="col">Borrow date</th>
                        <th scope="col">Return due</th>
                        <th scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody>
<?php
if ($userId > 0) {
$bq = $conn->query(
    "SELECT b.title, b.author, br.borrow_date, br.return_date FROM borrow br
     JOIN books b ON br.book_id = b.book_id
     WHERE br.user_id = " . (int) $userId . "
     ORDER BY br.borrow_date DESC"
);
} else {
    $bq = false;
}
if ($bq && $bq->num_rows > 0) {
    while ($row = $bq->fetch_assoc()) {
        $borrow = $row["borrow_date"];
        $dueRaw = $row["return_date"] ?? "";
        if ($dueRaw === "") {
            $dueRaw = date("Y-m-d", strtotime($borrow . " +14 days"));
        }
        [$label, $cls] = smartlib_borrow_row_status($dueRaw);
        echo "<tr><td>" . htmlspecialchars($row["title"]) . "</td><td>" . htmlspecialchars($row["author"]) . "</td>";
        echo "<td>" . htmlspecialchars(date("M j, Y", strtotime($borrow))) . "</td>";
        echo "<td>" . htmlspecialchars(date("M j, Y", strtotime($dueRaw))) . "</td>";
        echo '<td><span class="text-' . htmlspecialchars($cls) . ' fw-semibold">' . htmlspecialchars($label) . "</span></td></tr>\n";
    }
} else {
    echo '<tr><td colspan="5" class="text-center text-muted">No loans yet for this patron.</td></tr>';
}
?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="tab-pane fade" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                <h2 class="h5 fw-semibold text-primary mb-3">Profile (read-only)</h2>
                <p class="text-secondary small mb-3">These fields are stored in the <code>users</code> table. Staff can change them in <a href="admin.php">Admin</a>.</p>
                <?php if ($user): ?>
                <div class="table-responsive">
                  <table class="table table-bordered w-auto mb-0">
                    <tbody>
                      <tr><th scope="row">ID</th><td><?php echo (int) $user["id"]; ?></td></tr>
                      <tr><th scope="row">Name</th><td><?php echo htmlspecialchars($user["name"] ?? ""); ?></td></tr>
                      <tr><th scope="row">Age</th><td><?php echo $user["age"] !== null && $user["age"] !== "" ? htmlspecialchars((string) $user["age"]) : '<span class="text-muted">—</span>'; ?></td></tr>
                      <tr><th scope="row">Role</th><td><?php echo htmlspecialchars($user["role"] ?? ""); ?></td></tr>
                      <tr><th scope="row">Email</th><td><?php echo htmlspecialchars($user["email"] ?? ""); ?></td></tr>
                    </tbody>
                  </table>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0">No patron selected.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

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
      

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
