<?php

declare(strict_types=1);

namespace App\User\Application\Command\ForgotPassword;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\User\Application\Event\ForgotPasswordTokenCreated;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\ForgotPasswordTokenServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ForgotPasswordCommand::class)]
class ForgotPasswordCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ForgotPasswordTokenServiceInterface $forgotPasswordTokenService,
        private EventDispatcherInterface $eventDispatcher
    ){
    }

    public function __invoke(ForgotPasswordCommand|CommandInterface $command): void
    {
        $user = $this->userRepository->findByEmail($command->getEmail());
        if ($user === null) {
            throw new BadRequestException('User not found');
        }

        $forgotPasswordToken = $this->forgotPasswordTokenService->generateToken($user->getId());

        $this->eventDispatcher->dispatch(
            new ForgotPasswordTokenCreated(
                $user->getFirstName() . ' ' . $user->getLastName(),
                $user->getEmail(),
                $forgotPasswordToken->getToken()
            )
        );
    }
}
