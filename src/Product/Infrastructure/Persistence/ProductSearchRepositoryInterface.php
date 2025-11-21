<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Infrastructure\ReadModel\ProductSearch\ProductSearchReadModel;
use App\Shared\Application\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method ProductSearchReadModel|null findById(Uuid $id)
 */
interface ProductSearchRepositoryInterface extends RepositoryInterface
{

}
