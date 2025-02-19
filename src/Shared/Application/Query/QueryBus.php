<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

use Psr\Container\ContainerInterface;
use InvalidArgumentException;

class QueryBus
{
    public function __construct(private ContainerInterface $handlers)
    {
    }

    public function execute(QueryInterface $query): mixed
    {
        $handlerClass = get_class($query) . 'Handler';

        if (!$this->handlers->has($handlerClass)) {
            throw new InvalidArgumentException("No handler found for query " . get_class($query));
        }

        return $this->handlers->get($handlerClass)->handle($query);
    }
}
