<?php

require_once __DIR__ . '/DBManager.php';

class User
{   
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
     * @return array|null The user data if found, or null if not found.
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT * FROM user WHERE mail = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Finds a user by their ID.
     *
     * @param int $id The ID of the user to find.
     * @return array|null The user data if found, or null if not found.
     */
    public static function findById(int $id): ?array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
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
            $currentAvatar = $currentUser['avatar'] ?? null;

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
