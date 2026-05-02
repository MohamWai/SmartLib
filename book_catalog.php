<?php

/**
 * Represents one row from the `books` table (Part 4: class + array of objects + table renderer).
 */
class Book
{
    private int $bookId;
    private string $title;
    private string $author;
    private string $category;
    private string $summary;
    private ?string $fileName;

    public function __construct(
        int $bookId,
        string $title,
        string $author,
        string $category,
        string $summary = '',
        ?string $fileName = null
    ) {
        $this->bookId = $bookId;
        $this->title = $title;
        $this->author = $author;
        $this->category = $category;
        $this->summary = $summary;
        $this->fileName = $fileName;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) ($row['book_id'] ?? 0),
            (string) ($row['title'] ?? ''),
            (string) ($row['author'] ?? ''),
            (string) ($row['category'] ?? ''),
            (string) ($row['summary'] ?? ''),
            isset($row['file_name']) && $row['file_name'] !== '' ? (string) $row['file_name'] : null
        );
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function setBookId(int $bookId): void
    {
        $this->bookId = $bookId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /** Short line for search results (file attachment hint). */
    public function getAttachmentLabel(): string
    {
        return $this->fileName !== null && $this->fileName !== '' ? 'eBook file' : 'Catalogue';
    }
}

/**
 * Runs a SELECT and returns an array of Book instances.
 *
 * @return Book[]
 */
function smartlib_fetch_books_sql(mysqli $conn, string $sql): array
{
    $objects = [];
    $result = $conn->query($sql);
    if (!$result) {
        return $objects;
    }
    while ($row = $result->fetch_assoc()) {
        $objects[] = Book::fromRow($row);
    }
    return $objects;
}

/**
 * Iterates over Book objects and echoes table rows (main row + summary row).
 * Uses selection on empty array vs non-empty; each row uses object getters.
 */
function smartlib_render_book_search_table(array $books): void
{
    if (count($books) === 0) {
        echo '<tr><td colspan="4" class="text-center text-danger">No books found</td></tr>';
        return;
    }

    foreach ($books as $book) {
        if (!$book instanceof Book) {
            continue;
        }

        $title = htmlspecialchars($book->getTitle());
        $author = htmlspecialchars($book->getAuthor());
        $category = htmlspecialchars($book->getCategory());
        $status = htmlspecialchars($book->getAttachmentLabel());

        echo "<tr>\n";
        echo "    <td>{$title}</td>\n";
        echo "    <td>{$author}</td>\n";
        echo "    <td>{$category}</td>\n";
        echo "    <td>{$status}</td>\n";
        echo "</tr>\n";

        $summaryText = $book->getSummary();
        if ($summaryText === '') {
            $summaryText = 'No description available';
        }
        $summaryHtml = htmlspecialchars($summaryText);

        echo "<tr>\n";
        echo "    <td colspan=\"4\">{$summaryHtml}</td>\n";
        echo "</tr>\n";
    }
}
