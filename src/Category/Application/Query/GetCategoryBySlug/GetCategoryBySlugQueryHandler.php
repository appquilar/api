<?php

declare(strict_types=1);

namespace App\Category\Application\Query\GetCategoryBySlug;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use App\Category\Application\Transformer\CategoryTransformer;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCategoryBySlugQuery::class)]
class GetCategoryBySlugQueryHandler implements QueryHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private CategoryTransformer $categoryTransformer,
    ) {
    }

    public function __invoke(GetCategoryBySlugQuery|Query $query): GetCategoryBySlugQueryResult|QueryResult
    {
        $category = $this->categoryRepository->findBySlug($query->getSlug());

        if ($category === null) {
            throw new NotFoundException();
        }

        return new GetCategoryBySlugQueryResult(
            $this->categoryTransformer->transform($category)
        );
    }
}
