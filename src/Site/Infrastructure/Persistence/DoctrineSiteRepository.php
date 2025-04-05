<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Site\Domain\Entity\Site;

class DoctrineSiteRepository extends DoctrineRepository implements SiteRepositoryInterface
{
    public function getClass(): string
    {
        return Site::class;
    }
}
