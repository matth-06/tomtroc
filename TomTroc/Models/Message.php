<?php
require_once __DIR__ . '/DBManager.php';

class Message
{
    /**
     * Create a new message
     */
    public static function create(int $senderId, int $receiverId, string $content): bool
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('INSERT INTO messagerie (sender_id, receiver_id, content, `read`, created_at) VALUES (?, ?, ?, 0, NOW())');
        return $stmt->execute([$senderId, $receiverId, $content]);
    }

    /**
     * Get all messages between two users
     */
    public static function getConversation(int $userId1, int $userId2): array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare(
            'SELECT * FROM messagerie 
             WHERE (sender_id = ? AND receiver_id = ?) 
                OR (sender_id = ? AND receiver_id = ?) 
             ORDER BY created_at ASC'
        );
        $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
        return $stmt->fetchAll();
    }

    /**
     * Get all unique conversations for a user
     */
    public static function getConversations(int $userId): array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare(
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
        $lastMsgStmt = $pdo->prepare(
            'SELECT content, created_at, sender_id FROM messagerie 
             WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
             ORDER BY created_at DESC LIMIT 1'
        );

        foreach ($stmt->fetchAll() as $row) {
            if ($row['other_user_id'] && $row['nickname']) {
                // fetch last message for this conversation
                $lastMsgStmt->execute([$userId, $row['other_user_id'], $row['other_user_id'], $userId]);
                $last = $lastMsgStmt->fetch();

                $results[] = [
                    'id' => $row['other_user_id'],
                    'nickname' => $row['nickname'],
                    'avatar' => $row['avatar'],
                    'last_message' => $last ? $last['content'] : '',
                    'last_time' => $last ? $last['created_at'] : null,
                    'last_sender_id' => $last ? $last['sender_id'] : null,
                ];
            }
        }
        return $results;
    }

    /**
     * Mark messages as read
     */
    public static function markAsRead(int $userId1, int $userId2): void
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('UPDATE messagerie SET `read` = 1 WHERE sender_id = ? AND receiver_id = ?');
        $stmt->execute([$userId2, $userId1]);
    }

    /**
     * Get unread message count for a user
     */
    public static function getUnreadCount(int $userId): int
    {
        $pdo = DBManager::getInstance()->getPDO();
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM messagerie WHERE receiver_id = ? AND `read` = 0');
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
?>