<?php

declare(strict_types=1);

namespace App\Company\Application\Event;

use Symfony\Component\Uid\Uuid;

class CompanyCreated
{
    public function __construct(
        private Uuid $companyId,
        private Uuid $ownerId
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }
}
