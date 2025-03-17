<?php

declare(strict_types=1);

namespace App\Tests\Integration\Context;

use App\Tests\Factories\Category\Domain\Entity\PersistingCategoryFactory;
use Symfony\Component\Uid\Uuid;

trait CategoryContext
{
    public function givenItExistsACategoryWithId(Uuid $categoryId): void
    {
        PersistingCategoryFactory::createOne(['categoryId' => $categoryId]);
    }

    public function givenItExistsACategoryWithSlug(string $slug): void
    {
        PersistingCategoryFactory::createOne(['slug' => $slug]);
    }

    public function givenItExistsACategoryWithIdAndSlug(Uuid $categoryId, string $slug): void
    {
        PersistingCategoryFactory::createOne(['categoryId' => $categoryId, 'slug' => $slug]);
    }
}
