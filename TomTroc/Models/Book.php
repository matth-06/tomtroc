<?php

require_once __DIR__ . '/DBManager.php';

class Book
{
    private $id;
    private $titre;
    private $auteur;
    private $description;
    private $image;
    private $disponibilite;
    private $proprietaireId;
    private $proprietaire;

    public function __construct(
        int $id = 0,
        string $titre = '',
        string $auteur = '',
        string $description = '',
        string $image = '',
        string $disponibilite = '',
        int $proprietaireId = 0,
        string $proprietaire = ''
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->description = $description;
        $this->image = $image;
        $this->disponibilite = $disponibilite;
        $this->proprietaireId = $proprietaireId;
        $this->proprietaire = $proprietaire;
    }

    /**
     * Creates a Book object from an associative array.
     *
     * @param array $data The associative array containing book data.
     * @return Book The created Book object.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int)$data['id'] : 0,
            $data['titre'] ?? '',
            $data['auteur'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? '',
            $data['disponibilité'] ?? '',
            isset($data['propriétaireid']) ? (int)$data['propriétaireid'] : 0,
            $data['propriétaire'] ?? ''
        );
    }
    /**
     * Finds a book by its ID and returns it as a Book object.
     *
     * @param int $id The ID of the book to find.
     * @return Book|null The Book object if found, or null if not found.
     */

    public static function find(int $id): ?self
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre WHERE id = ?');
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        return $book ? self::fromArray($book) : null;
    }

    /**
     * GETTERS AND SETTERS
     */

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getAuteur(): string
    {
        return $this->auteur;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getDisponibilite(): string
    {
        return $this->disponibilite;
    }

    public function getProprietaireId(): int
    {
        return $this->proprietaireId;
    }

    public function getProprietaire(): string
    {
        return $this->proprietaire;
    }

    /**
     * Sets the properties of the book from an associative array.
     *
     * @param array $data The associative array containing book data.
     * @return void
     */

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setAuteur(string $auteur): void
    {
        $this->auteur = $auteur;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function setDisponibilite(string $disponibilite): void
    {
        $this->disponibilite = $disponibilite;
    }

    public function setProprietaireId(int $proprietaireId): void
    {
        $this->proprietaireId = $proprietaireId;
    }

    public function setProprietaire(string $proprietaire): void
    {
        $this->proprietaire = $proprietaire;
    }

    /**
     * Retrieves the latest books from the database.
     *
     * @param int $limit The number of latest books to retrieve.
     * @return Book[] An array of Book objects.
     */
    public static function getLatest(int $limit = 4): array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $books = $stmt->fetchAll();
        return array_map([self::class, 'fromArray'], $books);
    }

    /**
     * Searches for books based on the provided search term.
     *
     * @param string $search The search term to filter books by title or author.
     * @return Book[] An array of Book objects matching the search criteria.
     */
    public static function search(string $search = ''): array
    {
        $pdo = DBManager::getInstance()->getPDO();

        if ($search === '') {
            $stmt = $pdo->query('SELECT * FROM livre ORDER BY id DESC');
            $books = $stmt->fetchAll();
            return array_map([self::class, 'fromArray'], $books);
        }

        $stmt = $pdo->prepare('SELECT * FROM livre WHERE titre LIKE :search OR auteur LIKE :search ORDER BY id DESC');
        $stmt->execute(['search' => "%$search%"]);
        $books = $stmt->fetchAll();
        return array_map([self::class, 'fromArray'], $books);
    }

    /**
     * Finds books by their owner's ID.
     *
     * @param int $userId The ID of the user who owns the books.
     * @return Book[] An array of Book objects owned by the user.
     */
    public static function findByUser(int $userId): array
    {
        $stmt = DBManager::getInstance()->getPDO()->prepare('SELECT * FROM livre WHERE propriétaireid = ? ORDER BY id DESC');
        $stmt->execute([$userId]);

        $books = $stmt->fetchAll();
        return array_map([self::class, 'fromArray'], $books);
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
