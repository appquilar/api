<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Repository\RepositoryInterface;
use App\Shared\Application\Service\SlugifyServiceInterface;
use App\Shared\Domain\Entity\Entity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\LocaleAwareInterface;

class SymfonySlugifyService implements SlugifyServiceInterface
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function generate(string $text): string
    {
        return $this->slugger
            ->slug($text, '-', 'es')
            ->lower()
            ->toString();
    }

    public function validateSlugIsUnique(string $slug, RepositoryInterface $repository, ?Uuid $existentId = null): void
    {
        /** @var Entity $result */
        $result = $repository->findOneBy(['slug' => $slug]);

        if (
            $result !== null &&
            !$result->getId()->equals($existentId)
        ) {
            throw new BadRequestException('Slug must be unique');
        }
    }
}
