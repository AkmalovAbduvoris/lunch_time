<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use App\Models\Worker;

class Handler extends WebhookHandler
{
    public function start(): void
    {
        $this->reply("Salom! Lunch bot ishga tayyor!");
    }

    public function register(): void
    {
        $user = $this->message->from();
        Worker::updateOrCreate(
            ['telegram_id' => $user->id()],
            [
                'name'      => $user->firstName() . ($user->lastName() ?? ''),
                'username'  => $user->username(),
                'chat_id'   => $this->chat->chat_id,
                'is_active' => true,
            ]
        );
        $this->chat->message("Siz ro'yxatdan o'tdingiz!")->send();
    }
}
