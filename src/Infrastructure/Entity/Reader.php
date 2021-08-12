<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Entity;

use GimmeBook\Infrastructure\Core\Contract\ArrayableInterface;

class Reader implements ArrayableInterface
{
    private ?int $id;

    public function __construct(private string $login, private string $password, private int $roleId)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'roleId' => $this->roleId,
        ];
    }
}
