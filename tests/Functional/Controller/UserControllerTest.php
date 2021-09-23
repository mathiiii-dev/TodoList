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

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixtures::class]);
    }

    public function testAdminAccessUsersManagement()
    {
        /**@var UserRepository $users */
        $users = self::getContainer()->get(UserRepository::class);
        $user = $users->findOneBy(['username' => 'Mathias']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testNonAdminAccessUsersManagement()
    {
        /**@var UserRepository $users */
        $users = self::getContainer()->get(UserRepository::class);
        $user = $users->findOneBy(['username' => 'John']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminCreateUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['username' => 'Mathias']);

        $this->client->loginUser($testUser);

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
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['username' => 'Mathias']);

        $this->client->loginUser($testUser);

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

    public function testEditUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['username' => 'Mathias']);
        $user = $userRepository->findOneBy(['username' => 'John']);

        $this->client->loginUser($testUser);

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

        /** @var $newUser User */
        $newUser = $userRepository->find($user->getId());
        $this->assertSame("Jule", $newUser->getUserIdentifier());
        $this->assertSame("jule@user.com", $newUser->getEmail());
    }
}