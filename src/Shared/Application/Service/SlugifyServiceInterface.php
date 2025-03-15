<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

interface SlugifyServiceInterface
{
    public function generate(string $text): string;
    public function validateSlugIsUnique(string $slug, RepositoryInterface $repository, ?Uuid $existentId = null): void;
}
