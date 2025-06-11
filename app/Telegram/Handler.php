<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use App\Models\Worker;
use DefStudio\Telegraph\DTO\User;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class Handler extends WebhookHandler
{
    public function start(): void
    {
        $this->reply("Salom! Lunch bot ishga tayyor!");
    }

    public function register(): void
    {
        $user = $this->message->from();

        try {
            $status = $this->chat->memberInfo($user->id())->status();
            logger()->info("User status: " . $status);
        } catch (\Exception $e) {
            $this->chat->message("❌ Xatolik: " . $e->getMessage())->send();
            return;
        }

        $worker = Worker::updateOrCreate(
            ['telegram_id' => $user->id()],
            [
                'name'      => $user->firstName() . ($user->lastName() ?? ''),
                'username'  => $user->username(),
                'chat_id'   => $this->chat->chat_id,
                'is_active' => true,
            ]
        );

        if (in_array($status, ['administrator', 'creator'])) {
            $worker->syncRoles('admin');
        } else {
            $worker->syncRoles('worker');
        }

        $this->chat->message(
            "Siz ro'yxatdan o'tdingiz! Sizning rolingiz: " . $worker->getRoleNames()->first() . "\nBotga kirib start bosing!"
        )->send();
    }

    public function help(): void
    {
        $this->reply("Bu bot yordamida luch navbatga qo'shilasiz.\n\n" .
            "Buyurtma berish uchun /register buyrug'ini yuboring.");
    }

    // public function list(): void
    // {
    //     $workers = Worker::where('is_active', true)
    //         ->orderBy('order')
    //         ->get();

    //     $message = "📋 Navbatdagi ishchilar:\n\n";

    //     foreach ($workers as $index => $worker) {
    //         $position = $index + 1;
    //         $username = $worker->username ? "@{$worker->username}" : "username yo'q";
    //         $message .= "$position. {$worker->name} ($username)\n";
    //     }

    //     $keyboard = Keyboard::make()
    //         ->buttons([
    //             Button::make('🔄 Joylashuvni o‘zgartirish')->action('reorder_workers'),
    //             Button::make('⬅️ Orqaga')->action('show_main_menu'),
    //         ]);

    //     $this->chat->message($message)
    //         ->keyboard($keyboard)
    //         ->send();
    // }
    public function list(): void
    {
        $workers = Worker::where('is_active', true)->orderBy('order')->get();
        $message = "Hozirda navbatdagi ishchilar:\n";

        foreach ($workers as $worker) {
            $message .= "- {$worker->name} (@{$worker->username})\n";
        }

        $this->chat->html($message)->send();
    }

    protected function handleChatMemberJoined(User $member): void
    {
        $this->chat->html("Welcome {$member->firstName()}")->send();
    }
    protected function handleChatMemberLeft(User $member): void
    {
        $this->chat->html("{$member->firstName()} just left")->send();
    }
}
