<?php

declare(strict_types=1);

namespace App\Company\Application\Repository;

use App\Company\Domain\Entity\CompanyUser;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method CompanyUser|null findById(Uuid $id)
 * @method CompanyUser|null findOneBy(array $params)
 */
interface CompanyUserRepositoryInterface extends RepositoryInterface
{
    public function findCompanyIdByUserId(Uuid $userId): ?CompanyUser;

    /**
     * @return CompanyUser[]
     */
    public function findPaginatedUsersByCompanyId(Uuid $companyId, int $page, int $perPage): array;
    public function countUsersByCompanyId(Uuid $companyId): int;
}
