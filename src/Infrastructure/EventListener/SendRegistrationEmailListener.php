<?php

namespace App\Infrastructure\EventListener;

use App\Domain\Event\UserRegistered;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mime\Address;

#[AsEventListener(event: UserRegistered::class)]
final class SendRegistrationEmailListener
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EmailVerifier $emailVerifier,
        #[Autowire('%app.from_email%')]
        private readonly string $fromEmail,
        #[Autowire('%app.from_name%')]
        private readonly string $fromName,
    ) {
    }

    public function __invoke(UserRegistered $userRegistered): void
    {
        $user = $this->userRepository->findOneBy(['email' => $userRegistered->email]);

        if (!$user) {
            return;
        }

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            new TemplatedEmail()
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
