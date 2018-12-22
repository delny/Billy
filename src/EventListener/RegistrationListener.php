<?php

namespace App\EventListener;

use FOS\UserBundle\Event\GetResponseUserEvent;

class RegistrationListener
{
    /**
     * @param GetResponseUserEvent $event
     */
    public function disableUser(GetResponseUserEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);
    }
}
