<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Service;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Product\Application\Service\ProductCompanyServiceInterface;
use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Uid\Uuid;

class DoctrineProductCompanyService implements ProductCompanyServiceInterface
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {
    }

    public function getCompanyLocationByCompanyId(Uuid $companyId): GeoLocation
    {
        $company = $this->companyRepository->findById($companyId);

        return $company->getGeoLocation();
    }
}
