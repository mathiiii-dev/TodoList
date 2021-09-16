<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private User $user;

    private Task $task;

    protected function setUp(): void
    {
        $this->user = (new User())->setUsername('Mathias')
            ->setEmail('mail@mail.com')
            ->setPassword('password');

        $this->task = (new Task())->setTitle('un titre')
            ->setContent('un contenu')
            ->setIsDone(false)
            ->setUser($this->user);
    }

    public function testTask()
    {
        $this->assertEquals("un titre", $this->task->getTitle());
        $this->assertEquals("un contenu", $this->task->getContent());
        $this->assertIsBool(false, $this->task->getIsDone());
        $this->assertEquals($this->user, $this->task->getUser());
    }
}
