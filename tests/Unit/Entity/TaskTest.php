<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTest extends TestCase
{
    private User $user;
    private Task $task;
    private ValidatorInterface|RecursiveValidator $validation;

    protected function setUp(): void
    {
        $this->user = (new User())->setUsername('Mathias')
            ->setEmail('mail@mail.com')
            ->setPassword('password');

        $this->task = (new Task())->setTitle('un titre')
            ->setContent('un contenu')
            ->setIsDone(false)
            ->setCreatedAt()
            ->setUser($this->user);

        $this->validation = Validation::createValidatorBuilder()
            ->enableAnnotationMapping(true)
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
    }

    public function assertHasErrors(Task $task, int $number)
    {
        $errors = $this->validation->validate($task);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidTask()
    {
        $this->assertHasErrors($this->task, 0);
    }

    public function testTask()
    {
        $this->assertEquals('un titre', $this->task->getTitle());
        $this->assertEquals('un contenu', $this->task->getContent());
        $this->assertIsBool(false, $this->task->getIsDone());
        $this->assertEquals($this->user, $this->task->getUser());
        $this->assertEqualsWithDelta(new \DateTime(), $this->task->getCreatedAt(), 1);
    }

    public function testBlankContent()
    {
        $this->assertHasErrors($this->task->setContent(''), 1);
    }

    public function testBlankTitle()
    {
        $this->assertHasErrors($this->task->setTitle(''), 1);
    }
}
