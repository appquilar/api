<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Shared\Domain\Entity\Entity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "company_users")]
#[ORM\UniqueConstraint(name: 'company_user_idx', columns: ['company_id', 'user_id'])]
class CompanyUser extends Entity
{
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $companyId;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $userId;

    #[ORM\Column(type: 'string', enumType: CompanyUserRole::class)]
    private CompanyUserRole $companyUserRole;

    /**
     * @param Uuid $companyId
     * @param Uuid $userId
     * @param CompanyUserRole $companyUserRole
     */
    public function __construct(
        Uuid $companyId,
        Uuid $userId,
        CompanyUserRole $companyUserRole
    ) {
        parent::__construct(Uuid::v4());

        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->companyUserRole = $companyUserRole;
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function setCompanyId(Uuid $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $userId): void
    {
        $this->userId = $userId;
    }

    public function getCompanyUserRole(): CompanyUserRole
    {
        return $this->companyUserRole;
    }

    public function setCompanyUserRole(CompanyUserRole $companyUserRole): void
    {
        $this->companyUserRole = $companyUserRole;
    }
}
