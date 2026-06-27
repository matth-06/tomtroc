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
}