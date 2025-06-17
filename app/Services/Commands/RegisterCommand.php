<?php

namespace App\Services\Commands;

use App\Models\Worker;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\DTO\User;
use App\Services\ChatTypeService;

class RegisterCommand
{
    public function handleRegister(
        TelegraphChat $chat,
        User $user,
        ChatTypeService $type
    ): void {
        if ($type->isPrivate($chat)) {
            $chat->message("/register buyrug'i faqat guruhda ishlidi")->send();
            return;
        }
        try {
            $status = $chat->memberInfo($user->id())->status();
            logger()->info("User status: " . $status);
        } catch (\Exception $e) {
            $chat->message("❌ Xatolik: " . $e->getMessage())->send();
            return;
        }
        $worker = Worker::updateOrCreate(
            ['telegram_id' => $user->id()],
            [
                'name'      => $user->firstName() . ($user->lastName() ?? ''),
                'username'  => $user->username(),
                'is_active' => true,
            ]
        );
        if (in_array($status, ['administrator', 'creator'])) {
            $worker->syncRoles('admin');
        } else {
            $worker->syncRoles('worker');
        }
        $chat->message(
            "Siz ro'yxatdan o'tdingiz! Sizning rolingiz: " . $worker->getRoleNames()->first() . "\nBotga kirib start bosing!"
        )->send();
    }
}
