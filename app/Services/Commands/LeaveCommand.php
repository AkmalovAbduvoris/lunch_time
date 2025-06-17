<?php

namespace App\Services\Commands;

use App\Models\Worker;
use App\Services\ChatTypeService;
use DefStudio\Telegraph\DTO\User;
use DefStudio\Telegraph\Models\TelegraphChat;

class LeaveCommand
{
    public function handleLeave(
        TelegraphChat $chat,
        User $user,
        ChatTypeService  $chatTypeService
    ): void {
        logger()->info($user);
        if ($chatTypeService->isGroup($chat)) {
            $worker = Worker::where('telegram_id', $user->id())->first();

            if ($worker) {
                $worker->delete();
                $chat->message("❌ {$worker->name} tizimdan o'chirildi.")->send();
            } else {
                $chat->message("⚠️ Foydalanuvchi topilmadi: {$user->firstName()}")->send();
            }
            return;
        }
        $chat->message("/leave buyrug'i faqat guruhda ishlaydi")->send();
    }
}
