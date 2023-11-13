<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class AuthService
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
    public function authenticate( Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),            ]
        );
    }
}
