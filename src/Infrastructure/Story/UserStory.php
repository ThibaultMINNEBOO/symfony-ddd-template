<?php

namespace App\Infrastructure\Story;

use App\Domain\Entity\Enums\Role;
use App\Infrastructure\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne([
            'email' => 'root@example.com',
            'password' => 'rootpassword',
            'roles' => [Role::ADMIN->value],
        ]);
        UserFactory::createMany(10);
    }
}
