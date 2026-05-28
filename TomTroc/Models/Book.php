<?php

require_once __DIR__ . '/DBManager.php';

class Book
{
    public static function getLatest(int $limit = 4): array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

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

    public static function find(int $id): ?array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre WHERE id = ?');
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        return $book ?: null;
    }

    public static function findByUser(int $userId): array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre WHERE propriétaireid = ? ORDER BY id DESC');
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function create(string $title, string $author, string $description, string $image, string $availability, int $ownerId): bool
    {
        $pdo = DBManager::getInstance()->getPDO();

        $stmt = $pdo->prepare('SELECT nickname FROM user WHERE id = ?');
        $stmt->execute([$ownerId]);
        $ownerNickname = $stmt->fetchColumn() ?: '';

        $stmt = $pdo->prepare('INSERT INTO livre (titre, auteur, description, image, disponibilité, propriétaireid, propriétaire) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$title, $author, $description, $image, $availability, $ownerId, $ownerNickname]);
    }

    public static function update(int $bookId, string $title, string $author, string $description, string $image, string $availability): bool
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('UPDATE livre SET titre = ?, auteur = ?, description = ?, image = ?, disponibilité = ? WHERE id = ?');
        return $stmt->execute([$title, $author, $description, $image, $availability, $bookId]);
    }

    public static function delete(int $bookId): bool
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('DELETE FROM livre WHERE id = ?');
        return $stmt->execute([$bookId]);
    }
}
