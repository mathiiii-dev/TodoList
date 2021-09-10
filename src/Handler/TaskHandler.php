<?php

namespace App\Handler;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handleCreate(User $user, Task $task)
    {
        $task->setUser($user);
        $task->setIsDone(false);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function handleIsDone(Task $task)
    {
        $task->setIsDone(!$task->getIsDone());
        $this->entityManager->flush();
    }

    public function handleDelete(Task $task)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

}