<?php

namespace App\Infrastructure\DataFixtures;

use App\Infrastructure\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
    }
}
