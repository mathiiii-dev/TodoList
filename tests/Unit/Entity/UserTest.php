<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    private User $user;
    private Task $task;
    private Collection $tasks;
    private ?object $validation;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->user = (new User())->setUsername('Lionel')
            ->setEmail('mail@mail.com')
            ->setPassword('password')
            ->setRoles(['ROLE_ADMIN']);

        $this->task = (new Task())->setTitle('un titre')
            ->setContent('un contenu')
            ->setIsDone(false)
            ->setUser($this->user);

        $this->tasks = $this->user->getTasks();

        $this->validation = static::$kernel->getContainer()->get('validator');

    }

    public function assertHasErrors(User $user, int $number)
    {
        $errors = $this->validation->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidUser()
    {
        $this->assertEquals('Lionel', $this->user->getUserIdentifier());
        $this->assertEquals('mail@mail.com', $this->user->getEmail());
        $this->assertEquals('password', $this->user->getPassword());
        $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
        $this->assertEquals($this->tasks, $this->user->getTasks());
    }

    public function testInvalidFormatEmail()
    {
        $this->assertHasErrors($this->user->setEmail('m'), 1);
    }

    public function testBlankUsername()
    {
        $this->assertHasErrors($this->user->setUsername(''), 1);
    }

    public function testBlankPassword()
    {
        $this->assertHasErrors($this->user->setPassword(''), 1);
    }
}
