<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Domain\Entity\Product;
use Symfony\Component\Uid\Uuid;

interface ProductAuthorizationServiceInterface
{
    public function canView(Product $product, string $errorMessage): void;
    public function canViewIfPublic(Product $product, string $errorMessage): void;
    public function canEdit(Product $product, string $errorMessage): void;
    public function assignOwnership(Product $product, ?Uuid $companyId = null): void;
}
