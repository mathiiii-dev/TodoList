<?php

namespace App\Tests\Unit\Repository;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->loadFixtures([UserFixtures::class]);
    }

    public function testCoutNumberOfgUser()
    {
        $users = self::getContainer()->get(UserRepository::class);
        /**
         * @var UserRepository $users
         */
        $this->assertEquals(5, $users->count([]));
    }
}
