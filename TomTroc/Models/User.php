<?php

require_once __DIR__ . '/DBManager.php';

class User
{
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

    public static function findByEmail(string $email): ?array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT id, nickname, mail, password, avatar FROM user WHERE mail = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT id, nickname, mail, avatar FROM user WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

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
