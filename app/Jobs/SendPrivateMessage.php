<?php

namespace App\Jobs;

use App\Models\Worker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class SendPrivateMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Worker $worker) {}

public function handle(): void
{
    $bot = TelegraphBot::first();

    if (!$bot) {
        Log::error('Bot topilmadi!');
        return;
    }

    try {
        $chat = TelegraphChat::firstOrCreate(
            [
                'chat_id' => $this->worker->telegram_id,
                'telegraph_bot_id' => $bot->id,
            ],
            [
                'name' => $this->worker->name ?? 'User',
            ]
        );
        sleep(1);

        $chat->message("Salom {$this->worker->name}, bu avtomatik xabar!")->send();

        Log::info("Xabar yuborildi: {$this->worker->telegram_id} ({$this->worker->name})");

    } catch (\Exception $e) {
        Log::error("Xabar yuborishda xatolik (Telegram ID: {$this->worker->telegram_id}): " . $e->getMessage());
    }
}

}
