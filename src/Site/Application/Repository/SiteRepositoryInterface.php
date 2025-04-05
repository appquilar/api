<?php

declare(strict_types=1);

namespace App\Site\Application\Repository;

use App\Shared\Application\Repository\RepositoryInterface;
use App\Site\Domain\Entity\Site;
use Symfony\Component\Uid\Uuid;

/**
 * @method Site|null findById(Uuid $id)
 * @method Site|null findOneBy(Uuid $id)
 * @method Site[]    findAll()
 */
interface SiteRepositoryInterface extends RepositoryInterface
{
}
