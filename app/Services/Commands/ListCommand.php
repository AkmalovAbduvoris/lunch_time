<?php

namespace App\Services\Commands;

use App\Http\Controllers\Controller;
use App\Services\ChatTypeService;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use App\Services\WorkerRoleService;
use App\Models\Worker;

class ListCommand extends Controller
{
    public function handleList(
        TelegraphChat $chat,
        ChatTypeService $type,
        WorkerRoleService $roleService
    ): void {
        if ($type->isGroup($chat)) {
            $chat->message("Guruhda /list buyrug'i ishlamaydi. Iltimos, botga xabar yuboring.")->send();
            return;
        }

        $worker = Worker::where('telegram_id', '=', $chat->chat_id)->first();
        if (!$worker || (!$roleService->isAdmin($worker))) {
            $chat->message("❌ Ushbu bo'lim faqat adminlar uchun.")->send();
            return;
        }

        $workers = Worker::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($workers->isEmpty()) {
            $chat->message("Hozirda faol ishchilar mavjud emas.")->send();
            return;
        }

        $message = "👥 Faol ishchilar:\n";
        foreach ($workers as $index => $workerItem) {
            $position = $index + 1;
            $username = $workerItem->username ? "@{$workerItem->username}" : "username yo'q";
            $message .= "$position. {$workerItem->name} ($username)\n";
        }

        $keyboard = Keyboard::make()
            ->row([
                Button::make('🔄 Joylashuvni o‘zgartirish')->action('send_sms'),
            ])
            ->row([
                Button::make('⬅️ Orqaga')->action('show_main_menu'),
            ]);
        // $keyboard = ReplyKeyboard::make()
        //     ->button('Send Contact')->requestContact()
        //     ->button('Send Location')->requestLocation()
        //     ->persistent();

        // $chat->html($message)->replyKeyboard($keyboard)->send();
        $chat->html($message)->keyboard($keyboard)->send();
    }
}
