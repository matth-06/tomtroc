<?php
require_once __DIR__ . '/DBManager.php';

class Message
{
    private $id;
    private $senderId;
    private $receiverId;
    private $content;
    private $isRead;
    private $createdAt;

    public function __construct(
        int $id = 0,
        int $senderId = 0,
        int $receiverId = 0,
        string $content = '',
        bool $isRead = false,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->isRead = $isRead;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int)$data['id'] : 0,
            isset($data['sender_id']) ? (int)$data['sender_id'] : 0,
            isset($data['receiver_id']) ? (int)$data['receiver_id'] : 0,
            $data['content'] ?? '',
            isset($data['read']) ? (bool)$data['read'] : false,
            $data['created_at'] ?? null
        );
    }

    /**
     * GETTERS AND SETTERS
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function getReceiverId(): int
    {
        return $this->receiverId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

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
        $rows = $stmt->fetchAll();
        return array_map([self::class, 'fromArray'], $rows);
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