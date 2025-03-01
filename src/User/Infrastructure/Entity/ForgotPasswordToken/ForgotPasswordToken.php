<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Entity\ForgotPasswordToken;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'forgot_password_tokens')]
#[Index(name: "token_idx", columns: ["token"])]
class ForgotPasswordToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column(type: 'uuid')]
    private Uuid $siteId;

    #[ORM\Column(type: 'string', length: 300, unique: true)]
    private string $token;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    public function __construct(
        Uuid $userId,
        Uuid $siteId,
        string $token,
        \DateTimeImmutable $expiresAt
    ) {
        $this->id = Uuid::v4();
        $this->userId = $userId;
        $this->siteId = $siteId;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }
}
