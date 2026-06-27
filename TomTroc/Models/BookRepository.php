<?php

require_once __DIR__ . '/DBManager.php';
require_once __DIR__ . '/Book.php';

class BookRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DBManager::getInstance()->getPDO();
    }

    public function find(int $id): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT * FROM livre WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? Book::fromArray($row) : null;
    }

    public function getLatest(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM livre ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $books = $stmt->fetchAll();
        return array_map([Book::class, 'fromArray'], $books);
    }

    public function search(string $search = ''): array
    {
        if ($search === '') {
            $stmt = $this->pdo->query('SELECT * FROM livre ORDER BY id DESC');
            $books = $stmt->fetchAll();
            return array_map([Book::class, 'fromArray'], $books);
        }

        $stmt = $this->pdo->prepare('SELECT * FROM livre WHERE titre LIKE :search OR auteur LIKE :search ORDER BY id DESC');
        $stmt->execute(['search' => "%$search%"]); 
        $books = $stmt->fetchAll();
        return array_map([Book::class, 'fromArray'], $books);
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM livre WHERE propriétaireid = ? ORDER BY id DESC');
        $stmt->execute([$userId]);

        $books = $stmt->fetchAll();
        return array_map([Book::class, 'fromArray'], $books);
    }

    public function create(string $title, string $author, string $description, string $image, string $availability, int $ownerId): bool
    {
        $stmt = $this->pdo->prepare('SELECT nickname FROM user WHERE id = ?');
        $stmt->execute([$ownerId]);
        $ownerNickname = $stmt->fetchColumn() ?: '';

        $stmt = $this->pdo->prepare(
            'INSERT INTO livre (titre, auteur, description, image, disponibilité, propriétaireid, propriétaire) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([$title, $author, $description, $image, $availability, $ownerId, $ownerNickname]);
    }

    public function update(int $bookId, string $title, string $author, string $description, string $image, string $availability): bool
    {
        $stmt = $this->pdo->prepare('UPDATE livre SET titre = ?, auteur = ?, description = ?, image = ?, disponibilité = ? WHERE id = ?');
        return $stmt->execute([$title, $author, $description, $image, $availability, $bookId]);
    }

    public function delete(int $bookId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM livre WHERE id = ?');
        return $stmt->execute([$bookId]);
    }
}
