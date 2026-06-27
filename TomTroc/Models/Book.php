<?php

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
}

