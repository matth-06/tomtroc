<?php

require_once __DIR__ . '/DBManager.php';
require_once __DIR__ . '/User.php';

class UserRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DBManager::getInstance()->getPDO();
    }

    public function create(string $pseudo, string $email, string $password)
    {
        if ($this->existsByEmailOrPseudo($email, $pseudo)) {
            return 'Utilisateur déjà existant';
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO user (nickname, mail, password) VALUES (?, ?, ?)');
        $stmt->execute([$pseudo, $email, $hashedPassword]);

        return true;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE mail = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        return $user ? User::fromArray($user) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ? User::fromArray($user) : null;
    }

    public function existsByEmailOrPseudo(string $email, string $pseudo, ?int $excludeId = null): bool
    {
        if ($excludeId === null) {
            $stmt = $this->pdo->prepare('SELECT id FROM user WHERE mail = ? OR nickname = ?');
            $stmt->execute([$email, $pseudo]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id FROM user WHERE (mail = ? OR nickname = ?) AND id != ?');
            $stmt->execute([$email, $pseudo, $excludeId]);
        }

        return (bool) $stmt->fetch();
    }

    public function update(int $userId, string $newEmail, string $newPseudo, string $newPassword = '', ?array $newAvatar = null)
    {
        if ($this->existsByEmailOrPseudo($newEmail, $newPseudo, $userId)) {
            return 'Cet email ou pseudo est déjà utilisé par un autre compte.';
        }

        $stmt = $this->pdo->prepare('UPDATE user SET mail = ?, nickname = ? WHERE id = ?');
        $stmt->execute([$newEmail, $newPseudo, $userId]);

        $stmt = $this->pdo->prepare('UPDATE livre SET propriétaire = ? WHERE propriétaireid = ?');
        $stmt->execute([$newPseudo, $userId]);

        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare('UPDATE user SET password = ? WHERE id = ?');
            $stmt->execute([$hashedPassword, $userId]);
        }

        if ($newAvatar && $newAvatar['error'] === UPLOAD_ERR_OK) {
            $currentUser = $this->findById($userId);
            $currentAvatar = $currentUser ? $currentUser->getAvatar() : null;

            $extension = pathinfo($newAvatar['name'], PATHINFO_EXTENSION);
            $filename = $userId . '_' . time() . '.' . $extension;
            $uploadDir = __DIR__ . '/../assets/users/';
            $avatarPath = $uploadDir . $filename;
            $avatarUrl = 'assets/users/' . $filename;

            if (move_uploaded_file($newAvatar['tmp_name'], $avatarPath)) {
                $stmt = $this->pdo->prepare('UPDATE user SET avatar = ? WHERE id = ?');
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

        return true;
    }
}
