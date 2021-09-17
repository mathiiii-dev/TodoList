<?php

namespace App\Tests\Repository;

use App\DataFixtures\TaskFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testCout()
    {
        self::bootKernel();
        $this->loadFixtures([TaskFixtures::class]);
        $users = self::getContainer()->get(TaskRepository::class);
        /**
         * @var UserRepository $users
         */
        $this->assertEquals(10, $users->count([]));
    }
}
