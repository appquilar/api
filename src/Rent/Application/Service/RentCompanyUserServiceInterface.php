<?php declare(strict_types=1);

namespace App\Rent\Application\Service;

use Symfony\Component\Uid\Uuid;

interface RentCompanyUserServiceInterface
{
    public function userBelongsToCompany(Uuid $userId, Uuid $companyId): bool;
}
