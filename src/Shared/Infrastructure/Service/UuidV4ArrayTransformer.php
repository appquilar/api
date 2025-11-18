<?php declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\Uid\Uuid;

class UuidV4ArrayTransformer
{
    /**
     * @param Uuid[] $uuids
     * @return string[]
     */
    public static function toArray(array $uuids): array
    {
        return array_map(
            fn (Uuid $uuid) => $uuid->toString(),
            $uuids
        );
    }

    /**
     * @param string[] $uuids
     * @return Uuid[]
     */
    public static function fromArray(array $uuids): array
    {
        return array_map(
            fn (string $uuid) => Uuid::fromString($uuid),
            $uuids
        );
    }
}
