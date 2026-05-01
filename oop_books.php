<?php
include "db.php";

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

class BookRecord
{
    private int $bookId;
    private string $title;
    private string $author;
    private string $category;
    private float $price;

    public function __construct(int $bookId, string $title, string $author, string $category, float $price)
    {
        $this->bookId = $bookId;
        $this->title = $title;
        $this->author = $author;
        $this->category = $category;
        $this->price = $price;
    }

    public function getBookId(): int { return $this->bookId; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getCategory(): string { return $this->category; }
    public function getPrice(): float { return $this->price; }
}

function renderBooksTable(array $books): void
{
    echo '<table class="table table-bordered table-striped bg-white">';
    echo '<thead class="table-dark"><tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Price (OMR)</th></tr></thead><tbody>';
    if (count($books) === 0) {
        echo '<tr><td colspan="5">No books found.</td></tr>';
    } else {
        foreach ($books as $book) {
            echo "<tr>";
            echo "<td>" . e($book->getBookId()) . "</td>";
            echo "<td>" . e($book->getTitle()) . "</td>";
            echo "<td>" . e($book->getAuthor()) . "</td>";
            echo "<td>" . e($book->getCategory()) . "</td>";
            echo "<td>" . e(number_format($book->getPrice(), 2)) . "</td>";
            echo "</tr>";
        }
    }
    echo '</tbody></table>';
}

$bookObjects = [];
$result = $conn->query("SELECT book_id, title, author, category, price_omr FROM books ORDER BY title ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookObjects[] = new BookRecord(
            (int) $row["book_id"],
            (string) $row["title"],
            (string) $row["author"],
            (string) $row["category"],
            (float) $row["price_omr"]
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SmartLib - OOP Books Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="h3 mb-2">Books OOP Report</h1>
  <p class="text-muted mb-4">This page demonstrates class + array of objects + function-based table rendering.</p>
  <?php renderBooksTable($bookObjects); ?>
</div>
</body>
</html>
