<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Book.php';

class BookController extends Controller
{
    public function index(string $search = ''): void
    {
        $title = 'Nos livres à l\'échange';
        $books = Book::search($search);
        $this->render('template/livreEx.php', compact('title', 'books', 'search'));
    }

    public function show(int $id): void
    {
        $book = Book::find($id);

        if (!$book) {
            header('Location: index.php');
            exit();
        }

        $title = $book['titre'] ?? 'Livre';
        $this->render('template/detailLivre.php', compact('title', 'book'));
    }

    public function createForm(): void
    {
        $title = 'Ajouter un livre';
        $this->render('template/ajoutLivre.php', compact('title'));
    }

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

    public function editForm(int $id): void
    {
        $book = Book::find($id);

        if (!$book) {
            header('Location: index.php');
            exit();
        }

        $title = 'Modifier le livre';
        $this->render('template/editBook.php', compact('title', 'book'));
    }

    public function update(int $id, array $post, array $files): void
    {
        $book = Book::find($id);

        if (!$book) {
            header('Location: index.php');
            exit();
        }

        $imageName = $book['image'];
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

    public function delete(int $id): void
    {
        Book::delete($id);
        header('Location: index.php?action=monCompte');
        exit();
    }

    private function uploadImage(?array $file, string $currentImage = ''): ?string
    {
        if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../assets/book/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

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
