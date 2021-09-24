<?php

namespace App\Tests\Functional\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
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
        $this->fixtures = $this->loadFixtureFiles([__DIR__ . '/../../Fixtures/UserFixtures.yaml']);
    }

    public function testTasksPage()
    {
       $this->client->loginUser($this->fixtures['user-1']);

       $this->client->request('GET', '/tasks');
       $this->assertResponseIsSuccessful();
       $this->assertSelectorExists('.btn.btn-info');
    }

    public function testRedirectIfNotLoggedIn()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects(
            "http://localhost/login",
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
            'task[content]' => 'Un contenu'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testCreateInvalidTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => '',
            'task[content]' => ''
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('li', 'Vous devez saisir un titre');
    }

    public function testDeleteTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $taskRepository = static::getContainer()->get(TaskRepository::class);

        /**@var $userRepository UserRepository */
        $testTask = $taskRepository->findOneBy(['user' => $this->fixtures['user-1']]);

        $this->client->request('GET', '/tasks/' . $testTask->getId() . '/delete');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

    }

    public function testDeleteTaskWithNonCreatorUser()
    {
        $testUser = $this->fixtures['user-1'];

        $this->client->loginUser($testUser);

        $testTask = $this->fixtures['task-2'];

        $this->client->request('GET', '/tasks/' . $testTask->getId() . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testToggleTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $testTask = $this->fixtures['task-1'];
        $isDone = $testTask->getIsDone();

        $this->client->request('GET', '/tasks/' . $testTask->getId() . '/toggle');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotEquals($isDone, $testTask->getIsDone());
    }

    public function testEditTask()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $testTask = $this->fixtures['task-1'];

        $crawler = $this->client->request('GET', '/tasks/' . $testTask->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Une tache modifié',
            'task[content]' => 'Un contenu modifié'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

}
