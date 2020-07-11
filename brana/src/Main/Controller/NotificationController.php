<?php

namespace App\Main\Controller;

use App\Message\NotificationMessage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;


class NotificationController {

    public function __invoke(MessageBusInterface $bus) {
        $users = ['one', 'two', 'three'];
        $bus->dispatch(new NotificationMessage('a message', $users));
        return new JsonResponse(['message'=>'success']);
    }
}