<?php

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
            isset($data['id']) ? (int) $data['id'] : 0,
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
}
