<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Service;

use App\Product\Application\Repository\ProductRepositoryInterface;
use App\Rent\Application\Dto\RentProductDto;
use App\Rent\Application\Service\RentProductServiceInterface;
use App\Rent\Domain\Enum\RentOwnerType;
use Symfony\Component\Uid\Uuid;

class DoctrineRentProductService implements RentProductServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function getProductOwnershipByProductId(Uuid $productId): ?RentProductDto
    {
        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            return null;
        }

        return new RentProductDto(
            $productId,
            $product->belongsToUser() ? $product->getUserId() : $product->getCompanyId(),
            $product->belongsToUser() ? RentOwnerType::USER : RentOwnerType::COMPANY,
        );
    }
}
