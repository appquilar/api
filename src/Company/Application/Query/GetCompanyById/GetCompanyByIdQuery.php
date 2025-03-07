<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyById;

use App\Shared\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

class GetCompanyByIdQuery implements Query
{
    public function __construct(
        private Uuid $companyId
    ) {
    }

    public function getCompanyId(): Uuid
    {
        return $this->companyId;
    }

}
