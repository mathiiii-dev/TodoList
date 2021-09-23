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
        foreach ($users as $user) {
            for ($i = 0; $i < 2; ++$i) {
                $task = (new Task())->setUser($user)
                    ->setTitle('Le tache ' . $i . ' de ' . $user->getUserIdentifier())
                    ->setIsDone(false)
                    ->setContent('Le contenu de la tache ' . $i . ' de ' . $user->getUserIdentifier());
                $manager->persist($task);
            }
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
