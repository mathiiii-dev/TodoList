<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\UserFixtures;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    private KernelBrowser $client;
    private array $fixtures;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = $this->loadFixtureFiles([__DIR__ . '/../../Fixtures/UserTaskFixtures.yaml']);
    }

    public function testAdminAccessUsersManagement()
    {
        $this->client->loginUser($this->fixtures['user-admin']);

        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testNonAdminAccessUsersManagement()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNotLoggedInAccessUsersManagement()
    {
        $this->client->request('GET', '/users');
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND
        );
    }

    public function testAdminCreateUser()
    {
        $this->client->loginUser($this->fixtures['user-admin']);

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'test_user',
            'user[password][first]' => 'test_password',
            'user[password][second]' => 'test_password',
            'user[roles]' => 'ROLE_USER',
            'user[email]' => 'test@user.com',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testCreateinvalidUser()
    {
        $this->client->loginUser($this->fixtures['user-admin']);

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'test_user',
            'user[password][first]' => 'test',
            'user[password][second]' => 'password',
            'user[roles]' => 'ROLE_USER',
            'user[email]' => 'test@user.com',
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('li', 'Les deux mots de passe doivent correspondre.');
    }

    public function testNonAdminAccessCreateUser()
    {
        $this->client->loginUser($this->fixtures['user-2']);
        $user = $this->fixtures['user-1'];

        $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNotLoggedInAccessCreateUser()
    {
        $this->client->request('GET', '/users/create');
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND
        );
    }

    public function testEditUser()
    {
        $this->client->loginUser($this->fixtures['user-admin']);
        $user = $this->fixtures['user-1'];

        $crawler = $this->client->request('GET', '/users/' . $user->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'Jule',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[roles]' => 'ROLE_USER',
            'user[email]' => 'jule@user.com',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testNonAdminAccessEditUser()
    {
        $this->client->loginUser($this->fixtures['user-2']);
        $user = $this->fixtures['user-1'];

        $this->client->request('GET', '/users/' . $user->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testNotLoggedInAccessEditUser()
    {
        $user = $this->fixtures['user-1'];

        $this->client->request('GET', '/users/' . $user->getId() . '/edit');
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND
        );
    }
}
