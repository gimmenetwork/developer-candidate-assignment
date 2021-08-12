<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Entity\Security;

class RefreshToken
{
    private ?string $uuid;

    public function __construct(
        private int $userId,
        private \DateTimeImmutable $createdAt,
        private int $expiresIn,
        private string $deviceId,
    ) {
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }
}
