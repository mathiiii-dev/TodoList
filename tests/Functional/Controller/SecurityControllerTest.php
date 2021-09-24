<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    private KernelBrowser $client;
    private array $fixtures;

    protected function setUp(): void
    {
       $this->client = static::createClient();
       $this->fixtures = $this->loadFixtureFiles([__DIR__ . '/../../Fixtures/UserFixtures.yaml']);
    }

    public function testAccessLoginForm()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    /**
    public function testSuccesLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Mathias',
            '_password' => 'password'
        ]);

        $this->client->submit($form);

        $this->assertNotFalse(unserialize($this->client->getContainer()->get('session')->get('_security_main')));

        $this->assertResponseRedirects(
            "http://localhost/",
            Response::HTTP_FOUND
        );
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }**/

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username_not_found',
            '_password' => 'password_not_found'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testAlreadyLoggedInUser() {

        $testUser = $this->fixtures['user-1'];

        $this->client->loginUser($testUser);

        $this->client->request('GET', '/login');
        $this->assertResponseRedirects();
    }
}
