<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'username' => 'Mathias',
            'email' => 'mail@mail.com',
            'password' => [
                'first' => 'password',
                'second' => 'password',
            ],
            'roles' => 'ROLE_USER'
        ];

        $model = new User();

        $form = $this->factory->create(UserType::class, $model);

        $excepted = (new User())->setUsername('Mathias')->setEmail('mail@mail.com')->setPassword('password');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEquals($excepted, $model);
    }
}
