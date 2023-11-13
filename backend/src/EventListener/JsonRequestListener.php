<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;

class JsonRequestListener
{
    public function onKernelController(ControllerEvent $event)
    {

        $request = $event->getRequest();

        $request->headers->set('Content-Type', 'application/json');
    }
}
