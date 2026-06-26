<?php

require_once __DIR__ . '/DBManager.php';

class User
{
    private $id;
    private $nickname;
    private $mail;
    private $password;
    private $avatar;
    private $memberSince;

    public function __construct(
        int $id = 0,
        string $nickname = '',
        string $mail = '',
        string $password = '',
        ?string $avatar = null,
        ?string $memberSince = null
    ) {
        $this->id = $id;
        $this->nickname = $nickname;
        $this->mail = $mail;
        $this->password = $password;
        $this->avatar = $avatar;
        $this->memberSince = $memberSince;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int)$data['id'] : 0,
            $data['nickname'] ?? '',
            $data['mail'] ?? '',
            $data['password'] ?? '',
            $data['avatar'] ?? null,
            $data['member_since'] ?? null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getMemberSince(): ?string
    {
        return $this->memberSince;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function setMemberSince(?string $memberSince): void
    {
        $this->memberSince = $memberSince;
    }

    /**
     * Creates a new user in the database.
     *
     * @param string $pseudo The user's nickname.
     * @param string $email The user's email address.
     * @param string $password The user's password.
     * @return mixed True on success, or an error message if the user already exists.
     */
    public static function create(string $pseudo, string $email, string $password)
    {
        $pdo = DBManager::getInstance()->getPDO();

        if (self::existsByEmailOrPseudo($email, $pseudo)) {
            return 'Utilisateur déjà existant';
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO user (nickname, mail, password) VALUES (?, ?, ?)');
        $stmt->execute([$pseudo, $email, $hashedPassword]);

        return true;
    }

    /**
     * Finds a user by their email address.
     *
     * @param string $email The email address to search for.
     * @return User|null The User object if found, or null if not found.
     */
    public static function findByEmail(string $email): ?self
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT * FROM user WHERE mail = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        return $user ? self::fromArray($user) : null;
    }

    /**
     * Finds a user by their ID.
     *
     * @param int $id The ID of the user to find.
     * @return User|null The User object if found, or null if not found.
     */
    public static function findById(int $id): ?self
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ? self::fromArray($user) : null;
    }

    /**
     * Checks if a user exists by their email or nickname.
     *
     * @param string $email The email address to check.
     * @param string $pseudo The nickname to check.
     * @param int|null $excludeId The ID of the user to exclude from the check.
     * @return bool True if the user exists, false otherwise.
     */
    public static function existsByEmailOrPseudo(string $email, string $pseudo, ?int $excludeId = null): bool
    {
        $pdo = DBManager::getInstance()->getPDO();

        if ($excludeId === null) {
            $stmt = $pdo->prepare('SELECT id FROM user WHERE mail = ? OR nickname = ?');
            $stmt->execute([$email, $pseudo]);
        } else {
            $stmt = $pdo->prepare('SELECT id FROM user WHERE (mail = ? OR nickname = ?) AND id != ?');
            $stmt->execute([$email, $pseudo, $excludeId]);
        }

        return (bool) $stmt->fetch();
    }

    /**
     * Updates a user's information in the database.
     *
     * @param int $userId The ID of the user to update.
     * @param string $newEmail The new email address.
     * @param string $newPseudo The new nickname.
     * @param string $newPassword The new password (optional).
     * @param array|null $newAvatar The new avatar file (optional).
     * @return mixed True on success, or an error message if the update fails.
     */
    public static function update(int $userId, string $newEmail, string $newPseudo, string $newPassword = '', ?array $newAvatar = null)
    {
        $pdo = DBManager::getInstance()->getPDO();

        if (self::existsByEmailOrPseudo($newEmail, $newPseudo, $userId)) {
            return 'Cet email ou pseudo est déjà utilisé par un autre compte.';
        }

        $stmt = $pdo->prepare('UPDATE user SET mail = ?, nickname = ? WHERE id = ?');
        $stmt->execute([$newEmail, $newPseudo, $userId]);

        $stmt = $pdo->prepare('UPDATE livre SET propriétaire = ? WHERE propriétaireid = ?');
        $stmt->execute([$newPseudo, $userId]);

        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE user SET password = ? WHERE id = ?');
            $stmt->execute([$hashedPassword, $userId]);
        }

        if ($newAvatar && $newAvatar['error'] === UPLOAD_ERR_OK) {
            $currentUser = self::findById($userId);
            $currentAvatar = $currentUser ? $currentUser->getAvatar() : null;

            $extension = pathinfo($newAvatar['name'], PATHINFO_EXTENSION);
            $filename = $userId . '_' . time() . '.' . $extension; // nom unique
            $uploadDir = __DIR__ . '/../assets/users/';
            $avatarPath = $uploadDir . $filename;
            $avatarUrl = 'assets/users/' . $filename;

            if (move_uploaded_file($newAvatar['tmp_name'], $avatarPath)) {
                $stmt = $pdo->prepare('UPDATE user SET avatar = ? WHERE id = ?');
                $stmt->execute([$avatarUrl, $userId]);

                if ($currentAvatar && $currentAvatar !== 'assets/users/default-avatar.png') {
                    $oldAvatarFile = __DIR__ . '/../' . $currentAvatar;
                    if (file_exists($oldAvatarFile)) {
                        unlink($oldAvatarFile);
                    }
                }

                return true;
            }
        }
        // Si on arrive ici, les autres champs ont été mis à jour avec succès
        return true;
    }
}
