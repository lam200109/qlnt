<?php

// src/Event/NotificationEvent.php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotificationEvent extends Event
{
    private $message;
    private $iconClass;
    private $time;
    private $userRoles;

    public function __construct($message, $iconClass, $time, $userRoles)
    {
        $this->message = $message;
        $this->iconClass = $iconClass;
        $this->time = $time;
        $this->userRoles = $userRoles;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getIconClass()
    {
        return $this->iconClass;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getUserRoles()
    {
        return $this->userRoles;
    }
}
