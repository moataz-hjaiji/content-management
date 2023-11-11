<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function hashedPassword(User $user): User
    {
        $plainPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user,$plainPassword);
        $user->setPassword($hashedPassword);

        return $user;
    }
    public function isValidPassword(User $user,string $password):bool
    {
        return $this->passwordHasher->isPasswordValid($user,$password);
    }
}
