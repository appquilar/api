<?php

declare(strict_types=1);

namespace App\Shared\Application\Context;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Infrastructure\Security\UserRole;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

class UserGranted
{
    private static ?UserGranted $me = null;
    private ?User $user;
    private ?Company $company;
    private ?CompanyUser $companyUser;
    private ?string $token;

    private function __construct() {
        $this->clear();
    }

    public static function me(): UserGranted
    {
        if (null === static::$me) {
            static::$me = new self();
        }

        return static::$me;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getCompanyUser(): ?CompanyUser
    {
        return $this->companyUser;
    }

    public function setCompanyUser(?CompanyUser $companyUser): void
    {
        $this->companyUser = $companyUser;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function clear(): void
    {
        static::$me = null;
        $this->user = null;
        $this->company = null;
        $this->companyUser = null;
    }

    public function isAdmin(): bool
    {
        return in_array(UserRole::ADMIN, $this->user->getRoles());
    }

    public function worksAtThisCompany(Uuid $id): bool
    {
        return $this->company !== null &&
            $this->companyUser !== null &&
            $this->companyUser->getCompanyId()->equals($id) &&
            $this->companyUser->getStatus() === CompanyUserStatus::ACCEPTED;
    }

    public function isAdminAtThisCompany(Uuid $id): bool
    {
        return $this->company !== null &&
            $this->companyUser !== null &&
            $this->companyUser->getCompanyId()->equals($id) &&
            $this->companyUser->getRole() === CompanyUserRole::ADMIN &&
            $this->companyUser->getStatus() === CompanyUserStatus::ACCEPTED;
    }
}
