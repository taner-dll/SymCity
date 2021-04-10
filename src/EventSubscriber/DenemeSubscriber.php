<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

class DenemeSubscriber implements EventSubscriberInterface
{
    public function onSecurityAuthenticationFailure(AuthenticationFailureEvent $event)
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'security.authentication.failure' => 'onSecurityAuthenticationFailure',
        ];
    }
}
