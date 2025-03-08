<?php

declare(strict_types=1);

namespace App\Company\Application\Command\RemoveCompanyUser;

use App\Shared\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

class RemoveCompanyUserCommand implements Command
{
    public function __construct(
        private Uuid $companyId,
        private Uuid $userId
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
