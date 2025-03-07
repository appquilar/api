<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Entity\Company;
use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use Symfony\Component\Uid\Uuid;

class CompanyRepository extends DoctrineRepository implements CompanyRepositoryInterface
{
    public function getClass(): string
    {
        return Company::class;
    }

    public function findOneByOwnerId(Uuid $ownerId): ?Company
    {
        return $this->findOneBy(['ownerId' => $ownerId]);
    }
}
