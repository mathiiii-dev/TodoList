<?php

namespace App\Tests\Functional\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    use FixturesTrait;
    private KernelBrowser $client;
    private array $fixtures;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = $this->loadFixtureFiles([__DIR__ . '/../../Fixtures/UserTaskFixtures.yaml']);
    }

    public function testVisitingWhileLoggedIn()
    {
        $this->client->loginUser($this->fixtures['user-1']);

        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}
