<?php

require_once __DIR__ . '/DBManager.php';

class Book
{   
    /**
     * Retrieves the latest books from the database.
     *
     * @param int $limit The number of latest books to retrieve.
     * @return array An array of the latest books.
     */
    public static function getLatest(int $limit = 4): array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Searches for books based on the provided search term.
     *
     * @param string $search The search term to filter books by title or author.
     * @return array An array of books matching the search criteria.
     */
    public static function search(string $search = ''): array
    {
        $pdo = DBManager::getInstance()->getPDO();

        if ($search === '') {
            $stmt = $pdo->query('SELECT * FROM livre ORDER BY id DESC');
            return $stmt->fetchAll();
        }

        $stmt = $pdo->prepare('SELECT * FROM livre WHERE titre LIKE :search OR auteur LIKE :search ORDER BY id DESC');
        $stmt->execute(['search' => "%$search%"]);
        return $stmt->fetchAll();
    }

    /**
     * Finds a book by its ID.
     *
     * @param int $id The ID of the book to find.
     * @return array|null The book data if found, or null if not found.
     */
    public static function find(int $id): ?array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre WHERE id = ?');
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        return $book ?: null;
    }

    /**
     * Finds books by their owner's ID.
     *
     * @param int $userId The ID of the user who owns the books.
     * @return array An array of books owned by the user.
     */
    public static function findByUser(int $userId): array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre WHERE propriétaireid = ? ORDER BY id DESC');
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    /**
     * Creates a new book entry in the database.
     *
     * @param string $title The title of the book.
     * @param string $author The author of the book.
     * @param string $description The description of the book.
     * @param string $image The image filename of the book.
     * @param string $availability The availability status of the book.
     * @param int $ownerId The ID of the user who owns the book.
     * @return bool True if the book was created successfully, false otherwise.
     */
    public static function create(string $title, string $author, string $description, string $image, string $availability, int $ownerId): bool
    {
        $pdo = DBManager::getInstance()->getPDO();

        $stmt = $pdo->prepare('SELECT nickname FROM user WHERE id = ?');
        $stmt->execute([$ownerId]);
        $ownerNickname = $stmt->fetchColumn() ?: '';

        $stmt = $pdo->prepare('INSERT INTO livre (titre, auteur, description, image, disponibilité, propriétaireid, propriétaire) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$title, $author, $description, $image, $availability, $ownerId, $ownerNickname]);
    }

    /**
     * Updates an existing book entry in the database.
     *
     * @param int $bookId The ID of the book to update.
     * @param string $title The new title of the book.
     * @param string $author The new author of the book.
     * @param string $description The new description of the book.
     * @param string $image The new image filename of the book.
     * @param string $availability The new availability status of the book.
     * @return bool True if the book was updated successfully, false otherwise.
     */
    public static function update(int $bookId, string $title, string $author, string $description, string $image, string $availability): bool
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('UPDATE livre SET titre = ?, auteur = ?, description = ?, image = ?, disponibilité = ? WHERE id = ?');
        return $stmt->execute([$title, $author, $description, $image, $availability, $bookId]);
    }

    /**
     * Deletes a book entry from the database.
     *
     * @param int $bookId The ID of the book to delete.
     * @return bool True if the book was deleted successfully, false otherwise.
     */
    public static function delete(int $bookId): bool
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('DELETE FROM livre WHERE id = ?');
        return $stmt->execute([$bookId]);
    }
}
