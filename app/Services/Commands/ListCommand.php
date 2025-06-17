<?php

namespace App\Services\Commands;

use App\Http\Controllers\Controller;
use App\Services\ChatTypeService;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
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

        $message = "👥 Faol ishchilar:\n";
        $keyboard = Keyboard::make();

        $workers = Worker::where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($workers as $index => $workerItem) {
            $position = $index + 1;
            $username = $workerItem->username ? "@{$workerItem->username}" : "username yo'q";
            $message .= "$position. {$workerItem->name} ($username)\n";

            $row = [
                Button::make("{$position}. {$workerItem->name}")->action(''),
            ];

            if ($index > 0) {
                $row[] = Button::make("⬆️")->action('move_up')->param('id', $workerItem->id);
            }

            if ($index < $workers->count() - 1) {
                $row[] = Button::make("⬇️")->action('move_down')->param('id', $workerItem->id);
            }
            $keyboard->row($row);
        }

        $chat->html($message)->keyboard($keyboard)->send();
    }
}
