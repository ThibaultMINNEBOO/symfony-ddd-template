<?php

namespace App\Application\Command;

use App\Domain\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email', entityClass: User::class)]
class RegisterUserCommand
{
    public function __construct(
        public string $firstName = '',
        public string $lastName = '',
        public string $email = '',
        public string $password = '',
    ) {
    }
}
