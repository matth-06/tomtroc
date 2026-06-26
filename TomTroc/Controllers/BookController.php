<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/AuthService.php';
require_once __DIR__ . '/../Models/Book.php';

class BookController extends Controller
{
    protected $authService;
    /**
     * BookController constructor.
     * Initializes the AuthService instance.
     */
    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Displays the list of books available for exchange.
     *
     * @param string $search Optional search term to filter books.
     * @return void
     */
    public function index(string $search = ''): void
    {
        $title = 'Nos livres à l\'échange';
        $books = Book::search($search);
        $this->render('template/livreEx.php', compact('title', 'books', 'search'));
    }

    /**
     * Displays the details of a specific book.
     *
     * @param int $id The ID of the book to display.
     * @return void
     */
    public function show(int $id): void
    {
        $book = Book::find($id);

        if (!$book) {
            header('Location: index.php');
            exit();
        }

        $title = $book->getTitre() ?: 'Livre';
        $this->render('template/detailLivre.php', compact('title', 'book'));
    }

    /**
     * Displays the form to add a new book.
     *
     * @return void
     */
    public function createForm(): void
    {
        $this->authService->ensureAuthenticated();

        $title = 'Ajouter un livre';
        $this->render('template/ajoutLivre.php', compact('title'));
    }

    /**
     * Stores a new book in the database.
     *
     * @param array $post The POST data from the form submission.
     * @param array $files The FILES data from the form submission.
     * @param int $userId The ID of the authenticated user adding the book.
     * @return void
     */
    public function store(array $post, array $files, int $userId): void
    {
        if ($userId <= 0) {
            header('Location: index.php?action=login');
            exit();
        }

        $imageName = $this->uploadImage($files['image'] ?? null);
        $imageName = $imageName ?? '';

        Book::create(
            trim($post['title'] ?? ''),
            trim($post['author'] ?? ''),
            trim($post['description'] ?? ''),
            $imageName,
            trim($post['availability'] ?? ''),
            $userId
        );

        header('Location: index.php?action=monCompte');
        exit();
    }

    /**
     * Displays the form to edit an existing book.
     *
     * @param int $id The ID of the book to edit.
     * @return void
     */
    public function editForm(int $id): void
    {
        $this->authService->ensureAuthenticated();

        $book = Book::find($id);

        if (!$book) {
            header('Location: index.php');
            exit();
        }

        $title = $book->getTitre() ?: 'Modifier le livre';
        $this->render('template/editBook.php', compact('title', 'book'));
    }

    /**
     * Updates an existing book in the database.
     *
     * @param int $id The ID of the book to update.
     * @param array $post The POST data from the form submission.
     * @param array $files The FILES data from the form submission.
     * @return void
     */
    public function update(int $id, array $post, array $files): void
    {
        $book = Book::find($id);

        if (!$book) {
            header('Location: index.php');
            exit();
        }

        $imageName = $book->getImage();
        $newImage = $this->uploadImage($files['image'] ?? null, $imageName);
        if ($newImage !== null) {
            $imageName = $newImage;
        }

        Book::update(
            $id,
            trim($post['title'] ?? ''),
            trim($post['author'] ?? ''),
            trim($post['description'] ?? ''),
            $imageName,
            trim($post['availability'] ?? '')
        );

        header('Location: index.php?action=showBook&id=' . $id);
        exit();
    }

    /**
     * Deletes a book by its ID.
     *
     * @param int $id The ID of the book to delete.
     * @return void
     */
    public function delete(int $id): void
    {
        $book = Book::find($id);

        if ($book) {
            $image = $book->getImage();
            if (!empty($image)) {
                $uploadDir = __DIR__ . '/../assets/book/';
                $oldFile = $uploadDir . $image;
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
        }

        Book::delete($id);
        header('Location: index.php?action=monCompte');
        exit();
    }

    /**
     * Uploads an image file and returns the new filename or the existing filename if no new image is uploaded.
     *
     * @param array|null $file The uploaded file data.
     * @param string $currentImage The current image filename.
     * @return string|null The filename of the uploaded image or null if no image was uploaded.
     */
    private function uploadImage(?array $file, string $currentImage = ''): ?string
    {
        if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../assets/book/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete the old image if it exists
        if (!empty($currentImage)) {
            $oldFile = $uploadDir . $currentImage;
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('book_', true) . '.' . $extension;
        move_uploaded_file($file['tmp_name'], $uploadDir . $imageName);

        return $imageName;
    }
}
