<?php

namespace App\Application\UseCase\Auth;

use App\Application\Command\RegisterUserCommand;
use App\Domain\Entity\User;
use App\Domain\Event\UserRegistered;
use App\Domain\Repository\UserRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class RegisterUserHandler
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function handle(RegisterUserCommand $command): ?User
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher(User::class);

        $user = User::create(
            $command->email,
            $hasher->hash($command->password)
        );

        $this->userRepository->save($user);
        $this->eventDispatcher->dispatch(new UserRegistered($user->getEmail()));

        return $user;
    }
}
