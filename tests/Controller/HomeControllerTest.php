<?php

namespace App\Tests\Controller;

use App\Controller\HomeController;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        /**
         * @var $userRepository UserRepository
         */
        $testUser = $userRepository->findOneBy(['id' => 1]);

        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}
