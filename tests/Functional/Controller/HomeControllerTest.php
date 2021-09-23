<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testVisitingWhileLoggedIn()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**@var $userRepository UserRepository */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $this->client->loginUser($testUser);

        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}
