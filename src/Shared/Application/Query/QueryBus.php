<?php

declare(strict_types=1);

namespace App\Shared\Application\Query;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function query(Query $query): QueryResult
    {
        try {
            $result = $this->handle($query);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return $result;
    }
}
