<?php

namespace App\Tests\Unit\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'title' => 'Un titre',
            'content' => 'Un contenu',
        ];

        $model = new Task();

        $form = $this->factory->create(TaskType::class, $model);

        $excepted = (new Task())->setTitle('Un titre')
            ->setContent('Un contenu');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertTrue($form->isSubmitted());
        $this->assertEquals($excepted, $model);
    }
}
