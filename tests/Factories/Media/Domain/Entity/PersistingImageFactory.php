<?php

namespace App\Tests\Factories\Media\Domain\Entity;

use App\Media\Domain\Entity\Image;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Image>
 */
class PersistingImageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Image::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'imageId' => Uuid::v4(),
            'originalFilename' => self::faker()->text(255),
            'mimeType' => self::faker()->text(255),
            'size' => self::faker()->randomNumber(),
            'width' => self::faker()->randomNumber(),
            'height' => self::faker()->randomNumber()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this->with($this->defaults())
            // ->afterInstantiate(function(Image $image): void {})
        ;
    }
}
