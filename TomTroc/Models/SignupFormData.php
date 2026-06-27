<?php

class SignupFormData
{
    private $pseudo;
    private $email;

    public function __construct(string $pseudo = '', string $email = '')
    {
        $this->pseudo = $pseudo;
        $this->email = $email;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
