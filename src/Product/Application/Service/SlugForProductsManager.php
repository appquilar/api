<?php declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Shared\Application\Service\SlugifyServiceInterface;

class SlugForProductsManager
{
    public function __construct(
        private SlugifyServiceInterface $slugifyService,
    ) {
    }

    public function generateSlugForProduct(
        string $productSlug,
        string $shortId
    ): string
    {
        return $this->slugifyService->generate($productSlug . '-' . $shortId);
    }
}
