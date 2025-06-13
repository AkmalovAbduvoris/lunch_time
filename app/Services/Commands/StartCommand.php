<?php

namespace App\Services\Commands;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Services\ChatTypeService;
use DefStudio\Telegraph\Models\TelegraphChat;
use App\Services\WorkerRoleService;

class StartCommand extends Controller
{

    public function handleStart(TelegraphChat $chat, ChatTypeService $type, WorkerRoleService $role): void
    {
        if ($type->isGroup($chat)) {
            $chat->message("Salom guruhga xush kelibsiz\n" .
                           "Bu guruhda /register kamandasi sizni ro'yxatga qo'shadi")->send();
            return;
        }

        $worker = Worker::where('telegram_id', '=', $chat->chat_id)->first();
        if (!$worker || (!$role->isAdmin($worker))) {
            $chat->message("Salom botga xush kelibsiz bu botda mavjud kamandalar")->send();
            return;
        }
        $chat->message("Salom botga xush kelibsiz bu botda mavjud kamandalar\n" .
                       "/list ishchilarning obedga chiqish ro'yxati")->send();
    }
}
