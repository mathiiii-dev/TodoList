<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->userRepository->findAll();

        for ($i = 0; $i < 10; ++$i) {
            $user = array_rand($users);
            $task = (new Task())->setUser($users[$user])
                ->setTitle('Le titre ' . $i)
                ->setIsDone(false)
                ->setContent('Le contenu de la tache ' . $i);
            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
