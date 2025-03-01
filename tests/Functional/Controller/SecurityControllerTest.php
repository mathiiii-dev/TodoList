<?php

namespace App\Tests\Functional\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    private KernelBrowser $client;
    private array $fixtures;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = $this->loadFixtureFiles([__DIR__.'/../../Fixtures/UserTaskFixtures.yaml']);
    }

    public function testAccessLoginForm()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testSuccessLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Mathias',
            '_password' => 'password',
        ]);
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username_not_found',
            '_password' => 'password_not_found',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoggedInUserAccessloginForm()
    {
        $testUser = $this->fixtures['user-1'];
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }
}
