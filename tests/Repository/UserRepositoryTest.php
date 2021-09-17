<?php

namespace App\Tests\Repository;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testCout()
    {
        self::bootKernel();
        $this->loadFixtures([UserFixtures::class]);
        $users = self::getContainer()->get(UserRepository::class);
        /**
         * @var UserRepository $users
         */
        $this->assertEquals(5, $users->count([]));
    }
}
