<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    private Task $task;

    private Collection $tasks;

    protected function setUp(): void
    {
        $this->user = (new User())->setUsername('Mathias')
            ->setEmail('mail@mail.com')
            ->setPassword('password')
            ->setRoles(['ROLE_ADMIN']);

        $this->task = (new Task())->setTitle('un titre')
            ->setContent('un contenu')
            ->setIsDone(false)
            ->setUser($this->user);

        $this->user->addTask($this->task);
        $this->tasks = $this->user->getTasks();
    }

    public function testUser()
    {
        $this->assertEquals('Mathias', $this->user->getUserIdentifier());
        $this->assertEquals('mail@mail.com', $this->user->getEmail());
        $this->assertEquals('password', $this->user->getPassword());
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
        $this->assertEquals($this->tasks, $this->user->getTasks());
    }

}
