<?php


namespace App\EventListener;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecuritySubscriber implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?\Symfony\Component\HttpFoundation\Response
    {
        // Perform actions after a user has logged in
        return new RedirectResponse('/');
    }
}
