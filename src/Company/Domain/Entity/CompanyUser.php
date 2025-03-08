<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Domain\Entity\Entity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "company_users")]
#[ORM\Index(name: 'token_idx', columns: ['invitation_token'])]
#[ORM\Index(name: 'company_user_idx', columns: ['company_id', 'user_id'])]
class CompanyUser extends Entity
{
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $companyId;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?Uuid $userId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', enumType: CompanyUserRole::class)]
    private CompanyUserRole $role;

    #[ORM\Column(type: 'string', enumType: CompanyUserStatus::class)]
    private CompanyUserStatus $status;

    #[ORM\Column(type: 'time_immutable')]
    private \DateTimeImmutable $invitationExpiresAt;

    #[ORM\Column(type: 'string', length: 40)]
    private string $invitationToken;

    public function __construct(
        Uuid $companyId,
        CompanyUserRole $role,
        string $email,
        ?Uuid $userId = null,
        CompanyUserStatus $status = CompanyUserStatus::PENDING,
        \DateTimeImmutable $invitationExpiresAt = new \DateTimeImmutable('+7 days')
    ) {
        parent::__construct(Uuid::v4());

        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->role = $role;
        $this->email = $email;
        $this->status = $status;
        $this->invitationExpiresAt = $invitationExpiresAt;
        $this->invitationToken = $this->generateInvitationToken();
    }

    private function generateInvitationToken(): string
    {
        return sha1(
            $this->companyId .
            $this->role->value .
            $this->email .
            $this->invitationExpiresAt->getTimestamp() .
            bin2hex(random_bytes(16))
        );
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function setCompanyId(Uuid $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function setUserId(?Uuid $userId = null): void
    {
        $this->userId = $userId;
    }

    public function getRole(): CompanyUserRole
    {
        return $this->role;
    }

    public function setRole(CompanyUserRole $role): void
    {
        $this->role = $role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getStatus(): CompanyUserStatus
    {
        return $this->status;
    }

    public function setStatus(CompanyUserStatus $status): void
    {
        $this->status = $status;
    }

    public function getInvitationExpiresAt(): \DateTimeImmutable
    {
        return $this->invitationExpiresAt;
    }

    public function setInvitationExpiresAt(\DateTimeImmutable $invitationExpiresAt): void
    {
        $this->invitationExpiresAt = $invitationExpiresAt;
    }

    public function getInvitationToken(): string
    {
        return $this->invitationToken;
    }

    public function setInvitationToken(string $invitationToken): void
    {
        $this->invitationToken = $invitationToken;
    }
}
