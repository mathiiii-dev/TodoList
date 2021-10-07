<?php

namespace App\Tests\Functional\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    private KernelBrowser $client;
    private array $fixtures;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = $this->loadFixtureFiles([__DIR__.'/../../Fixtures/UserTaskFixtures.yaml']);
    }

    public function testAccessTasksPage()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-primary');
    }

    public function testNotLoggedInAccessTasksPage()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects(
            $_ENV['HOST_URL'].'/login',
            Response::HTTP_FOUND
        );
    }

    public function testCreateValidTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Une tâche',
            'task[content]' => 'Un contenu',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testCreateInvalidTaskWithNoTitle()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => '',
            'task[content]' => 'Un contenu',
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback', 'Vous devez saisir un titre');
    }

    public function testCreateInvalidTaskWithNoContent()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Un titre',
            'task[content]' => '',
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback', 'Vous devez saisir du contenu');
    }

    public function testNotLoggedInAccessCreateTask()
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertResponseRedirects(
            $_ENV['HOST_URL'].'/login',
            Response::HTTP_FOUND
        );
    }

    public function testSuccessfulDeleteTaskAsAuthor()
    {
        $this->client->loginUser($this->fixtures['user-admin']);

        $this->client->request('GET', '/tasks/'.$this->fixtures['task-admin']->getId().'/delete');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteTaskWithNonCreatorUser()
    {
        $testUser = $this->fixtures['user-1'];

        $this->client->loginUser($testUser);

        $testTask = $this->fixtures['task-2'];

        $this->client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNotLoggedInAccessDeleteTask()
    {
        $this->client->request('GET', '/tasks/'.$this->fixtures['task-1']->getId().'/delete');
        $this->assertResponseRedirects(
            $_ENV['HOST_URL'].'/login',
            Response::HTTP_FOUND
        );
    }

    public function testToggleTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $testTask = $this->fixtures['task-1'];
        $isDone = $testTask->getIsDone();

        $this->client->request('GET', '/tasks/'.$testTask->getId().'/toggle');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotEquals($isDone, $testTask->getIsDone());
    }

    public function testNotLoggedInAccessToggleTask()
    {
        $this->client->request('GET', '/tasks/'.$this->fixtures['task-1']->getId().'/toggle');
        $this->assertResponseRedirects(
            $_ENV['HOST_URL'].'/login',
            Response::HTTP_FOUND
        );
    }

    public function testEditTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $testTask = $this->fixtures['task-1'];

        $crawler = $this->client->request('GET', '/tasks/'.$testTask->getId().'/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Une tache modifié',
            'task[content]' => 'Un contenu modifié',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testNotLoggedInAccessEditTask()
    {
        $this->client->request('GET', '/tasks/'.$this->fixtures['task-1']->getId().'/edit');
        $this->assertResponseRedirects(
            $_ENV['HOST_URL'].'/login',
            Response::HTTP_FOUND
        );
    }
}
