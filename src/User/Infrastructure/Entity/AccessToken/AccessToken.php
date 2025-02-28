<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Entity\AccessToken;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'access_tokens')]
#[Index(name: "token_idx", columns: ["token"])]
class AccessToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 300, unique: true)]
    private string $token;

    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column(type: 'uuid')]
    private Uuid $siteId;

    #[ORM\Column(type: 'boolean')]
    private bool $revoked = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(Uuid $userId, Uuid $siteId, string $token)
    {
        $this->id = Uuid::v4();
        $this->userId = $userId;
        $this->siteId = $siteId;
        $this->token = $token;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getSiteId(): Uuid
    {
        return $this->siteId;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }
}
