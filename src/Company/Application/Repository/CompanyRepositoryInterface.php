<?php

declare(strict_types=1);

namespace App\Company\Application\Repository;

use App\Company\Domain\Entity\Company;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method Company|null findById(Uuid $id)
 * @method Company|null findOneBy(array $params)
 */
interface CompanyRepositoryInterface extends RepositoryInterface
{
}
