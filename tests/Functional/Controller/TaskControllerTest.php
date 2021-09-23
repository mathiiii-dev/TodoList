<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Unit\Repository\TaskRepositoryTest;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixtures::class,TaskFixtures::class]);
    }

    public function testTasksPage()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

       $this->client->loginUser($testUser);

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
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $this->client->loginUser($testUser);

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
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $this->client->loginUser($testUser);

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
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $this->client->loginUser($testUser);

        $taskRepository = static::getContainer()->get(TaskRepository::class);

        /**@var $userRepository UserRepository */
        $testTask = $taskRepository->findOneBy(['user' => $testUser]);

        $this->client->request('GET', '/tasks/' . $testTask->getId() . '/delete');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

    }

    public function testDeleteTaskWithNonCreatorUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);
        $creator = $userRepository->findOneBy(['id' => 2]);


        $this->client->loginUser($testUser);

        $taskRepository = static::getContainer()->get(TaskRepository::class);

        /**@var $userRepository UserRepository */
        $testTask = $taskRepository->findOneBy(['user' => $creator]);

        $this->client->request('GET', '/tasks/' . $testTask->getId() . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testToggleTask()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $this->client->loginUser($testUser);

        /**@var $userRepository UserRepository */
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        /**@var $testTask Task*/
        $testTask = $taskRepository->findOneBy(['id' => 1]);
        $isDone = $testTask->getIsDone();

        $this->client->request('GET', '/tasks/' . $testTask->getId() . '/toggle');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotEquals($isDone, $testTask->getIsDone());
    }

    public function testEditTask()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $this->client->loginUser($testUser);

        /**@var $userRepository UserRepository */
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        /**@var $testTask Task*/
        $testTask = $taskRepository->findOneBy(['id' => 1]);

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

        /** @var $newTask Task */
        $newTask = $taskRepository->find($testTask->getId());
        $this->assertSame("Une tache modifié", $newTask->getTitle());
        $this->assertSame("Un contenu modifié", $newTask->getContent());
    }

}
