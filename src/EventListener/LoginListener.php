<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Xử lý sự kiện khi người dùng đăng nhập
    }
}
