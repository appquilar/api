<?php

declare(strict_types=1);

namespace App\Media\Application\Repository;

use App\Media\Domain\Entity\Image;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method Image|null findOneBy(array $params)
 * @method Image|null findById(Uuid $id)
 */
interface ImageRepositoryInterface extends RepositoryInterface
{
}
