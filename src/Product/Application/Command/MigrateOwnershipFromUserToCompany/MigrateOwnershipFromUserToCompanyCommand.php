<?php declare(strict_types=1);

namespace App\Product\Application\Command\MigrateOwnershipFromUserToCompany;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class MigrateOwnershipFromUserToCompanyCommand implements Command
{
    public function __construct(
        private Uuid $userId,
        private Uuid $companyId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }
}
