<?php

declare(strict_types=1);

namespace App\Product\Domain\Service;

use Symfony\Component\Uid\Uuid;

interface CompanyUserServiceInterface
{
    public function userBelongsToCompany(Uuid $userId, Uuid $companyId): bool;
}
