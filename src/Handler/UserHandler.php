<?php

namespace App\Handler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    public function handleCreate(User $user, Request $request)
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $request->get('user')['password']['first']
        ));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function handleEdit(User $user, Request $request)
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $request->get('user')->getPassword()
        ));

        $this->entityManager->flush();
    }

}