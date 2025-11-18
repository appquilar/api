<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class TransactionListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[AsEventListener(event: RequestEvent::class)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->entityManager->beginTransaction();
    }

    #[AsEventListener(event: ResponseEvent::class)]
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->commit();
            $this->entityManager->flush();
        }
    }

    #[AsEventListener(event: ExceptionEvent::class, priority: 1000)]
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
    }
}
