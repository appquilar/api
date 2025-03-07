<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence;

use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Domain\Entity\CompanyUser;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class CompanyUserRepository extends DoctrineRepository implements CompanyUserRepositoryInterface
{
    public function getClass(): string
    {
        return CompanyUser::class;
    }

    public function findCompanyIdByUserId(Uuid $userId): ?CompanyUser
    {
        return $this->findOneBy(['userId' => $userId]);
    }
}
