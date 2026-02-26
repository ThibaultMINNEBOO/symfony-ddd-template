<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<User>
 */
interface UserRepositoryInterface extends ObjectRepository
{
    public function save(User $user): void;
}
