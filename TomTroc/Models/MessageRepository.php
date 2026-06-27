<?php

require_once __DIR__ . '/DBManager.php';
require_once __DIR__ . '/Message.php';
require_once __DIR__ . '/Conversation.php';

class MessageRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DBManager::getInstance()->getPDO();
    }

    public function create(int $senderId, int $receiverId, string $content): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO messagerie (sender_id, receiver_id, content, `read`, created_at) VALUES (?, ?, ?, 0, NOW())');
        return $stmt->execute([$senderId, $receiverId, $content]);
    }

    public function getConversation(int $userId1, int $userId2): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM messagerie 
             WHERE (sender_id = ? AND receiver_id = ?) 
                OR (sender_id = ? AND receiver_id = ?) 
             ORDER BY created_at ASC'
        );
        $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
        $rows = $stmt->fetchAll();

        return array_map([Message::class, 'fromArray'], $rows);
    }

    public function getConversations(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT DISTINCT 
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as other_user_id,
                (SELECT nickname FROM user WHERE id = other_user_id) as nickname,
                (SELECT avatar FROM user WHERE id = other_user_id) as avatar
             FROM messagerie 
             WHERE sender_id = ? OR receiver_id = ?
             ORDER BY created_at DESC'
        );
        $stmt->execute([$userId, $userId, $userId]);

        $results = [];
        $lastMsgStmt = $this->pdo->prepare(
            'SELECT content, created_at, sender_id FROM messagerie 
             WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
             ORDER BY created_at DESC LIMIT 1'
        );

        foreach ($stmt->fetchAll() as $row) {
            if ($row['other_user_id'] && $row['nickname']) {
                $lastMsgStmt->execute([$userId, $row['other_user_id'], $row['other_user_id'], $userId]);
                $last = $lastMsgStmt->fetch();

                $results[] = new Conversation(
                    (int) $row['other_user_id'],
                    $row['nickname'],
                    $row['avatar'] ?? null,
                    $last ? $last['content'] : '',
                    $last ? $last['created_at'] : null,
                    $last ? (int) $last['sender_id'] : null
                );
            }
        }

        return $results;
    }

    public function findConversationUser(int $userId, int $otherUserId): ?Conversation
    {
        $stmt = $this->pdo->prepare('SELECT id, nickname, avatar FROM user WHERE id = ?');
        $stmt->execute([$otherUserId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Conversation(
            (int) $row['id'],
            $row['nickname'] ?? '',
            $row['avatar'] ?? null,
            '',
            null,
            null
        );
    }

    public function markAsRead(int $userId1, int $userId2): void
    {
        $stmt = $this->pdo->prepare('UPDATE messagerie SET `read` = 1 WHERE sender_id = ? AND receiver_id = ?');
        $stmt->execute([$userId2, $userId1]);
    }

    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM messagerie WHERE receiver_id = ? AND `read` = 0');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
