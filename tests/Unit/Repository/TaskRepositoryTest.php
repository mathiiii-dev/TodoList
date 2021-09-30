<?php

namespace App\Tests\Unit\Repository;

use App\DataFixtures\TaskFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->loadFixtures([TaskFixtures::class]);
    }

    public function testCountTask()
    {
        $users = self::getContainer()->get(TaskRepository::class);

        /** @var UserRepository $users */
        $this->assertEquals(10, $users->count([]));
    }
}
