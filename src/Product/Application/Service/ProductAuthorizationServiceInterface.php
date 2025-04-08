<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Domain\Entity\Product;

interface ProductAuthorizationServiceInterface
{
    public function canView(Product $product): bool;
    public function canEdit(Product $product): bool;
}
