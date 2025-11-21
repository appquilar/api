<?php

namespace App\Tests\Integration\Context;

use App\Product\Domain\ValueObject\PublicationStatus;
use App\Tests\Factories\Product\Domain\Entity\PersistingProductFactory;
use Symfony\Component\Uid\Uuid;

trait ProductContext
{
    public function givenItExistsAProductWithIdBelongingToAnUserId(Uuid $productId, Uuid $userId): void
    {
        $this->givenItExistsAProductWithParams(['productId' => $productId, 'userId' => $userId]);
    }

    public function givenItExistsAProductWithIdBelongingToACompanyId(Uuid $productId, Uuid $companyId): void
    {
        $this->givenItExistsAProductWithParams(['productId' => $productId, 'companyId' => $companyId]);
    }

    public function givenItExistsAProductWithSlugBelongingToAnUser(string $slug, Uuid $userId): void
    {
        $this->givenItExistsAProductWithParams(['slug' => $slug, 'userId' => $userId]);
    }

    public function givenItExistsAProductWithSlugAndShortIdBelongingToAnUserAndPublished(string $slug, Uuid $userId, string $shortId): void
    {
        $this->givenItExistsAProductWithParams([
            'slug' => $slug . '-' . $shortId,
            'shortId' => $shortId,
            'userId' => $userId,
            'publicationStatus' => PublicationStatus::published()
        ]);
    }

    public function givenItExistsAProductWithSlugAndShortIdBelongingToAnUser(string $slug, Uuid $userId, string $shortId): void
    {
        $this->givenItExistsAProductWithParams([
            'slug' => $slug . '-' . $shortId,
            'shortId' => $shortId,
            'userId' => $userId,
        ]);
    }

    public function givenItExistsAProductWithSlugBelongingToACompanyAndCategory(string $slug, Uuid $companyId, Uuid $categoryId): void
    {
        $this->givenItExistsAProductWithParams(['slug' => $slug, 'companyId' => $companyId, 'categoryId' => $categoryId]);
    }

    public function givenItExistsAProductWithParams(array $params): void
    {
        PersistingProductFactory::createOne($params);
    }

    public function givenItExistsAPublishedProductWithIdAndCategoryIdBelongingToAnUser(Uuid $productId, Uuid $categoryId, Uuid $userId): void
    {
        $this->givenItExistsAProductWithParams(['productId' => $productId, 'categoryId' => $categoryId, 'publicationStatus' => PublicationStatus::published(), 'userId' => $userId]);
    }
}