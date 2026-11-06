<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseHeaderListener implements EventSubscriberInterface
{
    private $controller;

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.controller' => 'onKernelController',
            'kernel.response' => 'onKernelResponse'
        );
    }

    public function onKernelController(ControllerEvent $event)
    {
        $this->controller = $event->getController();
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMasterRequest() || !is_array($this->controller)) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->add(array(
            'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache'
        ));
    }
}