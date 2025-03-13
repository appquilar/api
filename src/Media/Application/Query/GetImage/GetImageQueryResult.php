<?php

declare(strict_types=1);

namespace App\Media\Application\Query\GetImage;

use App\Shared\Application\Query\QueryResult;

class GetImageQueryResult implements QueryResult
{
    public function __construct(
        private mixed  $path,
    ) {
    }

    public function getPath(): mixed
    {
        return $this->path;
    }
}
