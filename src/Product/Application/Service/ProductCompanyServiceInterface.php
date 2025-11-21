<?php declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Uid\Uuid;

interface ProductCompanyServiceInterface
{
    public function getCompanyLocationByCompanyId(Uuid $companyId): GeoLocation;
}
