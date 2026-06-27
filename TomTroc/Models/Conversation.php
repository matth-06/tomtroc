<?php

class Conversation
{
    private $id;
    private $nickname;
    private $avatar;
    private $lastMessage;
    private $lastTime;
    private $lastSenderId;

    public function __construct(
        int $id = 0,
        string $nickname = '',
        ?string $avatar = null,
        string $lastMessage = '',
        ?string $lastTime = null,
        ?int $lastSenderId = null
    ) {
        $this->id = $id;
        $this->nickname = $nickname;
        $this->avatar = $avatar;
        $this->lastMessage = $lastMessage;
        $this->lastTime = $lastTime;
        $this->lastSenderId = $lastSenderId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : 0,
            $data['nickname'] ?? '',
            $data['avatar'] ?? null,
            $data['last_message'] ?? '',
            $data['last_time'] ?? null,
            isset($data['last_sender_id']) ? (int) $data['last_sender_id'] : null
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getLastMessage(): string
    {
        return $this->lastMessage;
    }

    public function getLastTime(): ?string
    {
        return $this->lastTime;
    }

    public function getLastSenderId(): ?int
    {
        return $this->lastSenderId;
    }
}
