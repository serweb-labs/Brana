<?php

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{
    public function __invoke(NotificationMessage $notification) {
        foreach ($notification->getUsers() as $user) {
            echo 'Notification sent to ' . $user . '\n';
            sleep(5);
        }
    }
}