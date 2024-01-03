<?php


// src/EventSubscriber/NotificationSubscriber.php

namespace App\EventSubscriber;

use App\Event\NotificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            NotificationEvent::class => 'onNotification',
        ];
    }

    public function onNotification(NotificationEvent $event)
    {
        $message = $event->getMessage();
        $iconClass = $event->getIconClass();
        $time = $event->getTime();
        $userRoles = $event->getUserRoles();

        foreach ($userRoles as $roles) {
            switch ($roles) {
                case 'ADMIN':
                case 'NHANVIENBANTHUOC':
                case 'NHANVIENQUANLYKHACHHANG':
                case 'NHANVIENBAOCAOTAICHINH':
                case 'NHANVIENNHAPHANG':
                    $this->addNotification($iconClass, $message, $time);
                    break;
                // Thêm các trường hợp xử lý khác tương ứng với các quyền khác
            }
        }
    }

    private function addNotification($iconClass, $message, $time)
    {
        // Thực hiện xử lý để hiển thị thông báo tương ứng với từng quyền
        // Đây có thể là đoạn mã JavaScript để thêm thông báo vào danh sách
        echo "<div class='nk-notification-item dropdown-inner'>
                  <div class='nk-notification-icon'>
                      <em class='{$iconClass}'></em>
                  </div>
                  <div class='nk-notification-content'>
                      <div class='nk-notification-text'>
                          {$message}
                      </div>
                      <div class='nk-notification-time'>{$time}</div>
                  </div>
              </div>";
    }
}
