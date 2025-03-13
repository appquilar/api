<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventSubscriber;

use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Service\ResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class ExceptionSubscriber
{
    private const EXCEPTIONS = [
        BadRequestException::class => [
            'handlers' => 'badRequest',
            'logLevel' => 'notice'
        ],
        UnauthorizedException::class => [
            'handlers' => 'unauthorized',
            'logLevel' => 'notice'
        ],
        NotFoundException::class => [
            'handlers' => 'notFound',
            'logLevel' => 'notice'
        ],
    ];

    public function __construct(
        private ResponseService $jsonResponse,
        private LoggerInterface $logger
    ) {
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 10)]
    public function processException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof InvalidArgumentException && str_contains($exception->getMessage(), 'must belong to a backed enumeration')) {
            $exception = new BadRequestException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $config = self::EXCEPTIONS[$exception::class] ?? ['handlers' => 'genericError'];

        $response = $this->jsonResponse->{$config['handlers']}($exception->getMessage());
        $event->setResponse($response);
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 0)]
    public function logException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $config = self::EXCEPTIONS[$exception::class] ?? ['logLevel' => 'error'];

        $this->logger->log(
            $config['logLevel'],
            sprintf(
                'Exception [%s]: %s in %s:%d',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ),
            ['exception' => $exception]
        );
    }
}
