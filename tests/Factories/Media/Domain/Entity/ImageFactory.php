<?php

declare(strict_types=1);

namespace App\Tests\Factories\Media\Domain\Entity;

final class ImageFactory extends PersistingImageFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this->withoutPersisting();
    }
}
