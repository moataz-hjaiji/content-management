<?php

namespace App\Listener;


use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class TokenListener {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function onFlush(LifecycleEventArgs $args) {

        $currentUser = $this->tokenStorage->getToken()->getUser();

        var_dump($currentUser); // prints a UserInterface object
    }

}
