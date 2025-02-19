<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatch(CommandInterface $command): void
    {
        $this->bus->dispatch($command);
    }
}
