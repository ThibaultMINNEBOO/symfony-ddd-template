<?php

namespace App\Domain\Event;

class UserRegistered
{
    public function __construct(
        public readonly string $email,
    ) {
    }
}
