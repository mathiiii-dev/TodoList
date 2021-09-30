<?php

namespace App\Handler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHandler
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    public function handleCreate(User $user)
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function handleEdit(User $user)
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));

        $this->entityManager->flush();
    }
}
