<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Service;

use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Rent\Application\Service\RentCompanyUserServiceInterface;
use Symfony\Component\Uid\Uuid;

class DoctrineRentCompanyUserService implements RentCompanyUserServiceInterface
{
    public function __construct(
        private CompanyUserRepositoryInterface $companyUserRepository
    ) {
    }

    public function userBelongsToCompany(Uuid $userId, Uuid $companyId): bool
    {
        return $this->companyUserRepository->findOneBy([
            'companyId' => $companyId,
            'userId' => $userId,
            'status' => CompanyUserStatus::ACCEPTED
        ]) !== null;
    }
}
