<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\DTO\User;
use App\Services\Commands\StartCommand;
use App\Services\Commands\ListCommand;
use App\Services\Commands\RegisterCommand;
use App\Services\ChatTypeService;
use App\Services\Commands\LeaveCommand;
use App\Services\WorkerRoleService;

class Handler extends WebhookHandler
{
    public function start(): void
    {
        app(StartCommand::class)->handleStart($this->chat, app(ChatTypeService::class), app(WorkerRoleService::class));
    }

    public function register(): void
    {
        app(RegisterCommand::class)->handleRegister($this->chat, $this->message->from(), app(ChatTypeService::class));
    }

    public function help(): void
    {
        $this->reply("Bu bot yordamida luch navbatga qo'shilasiz.\n\n" .
            "Buyurtma berish uchun /register buyrug'ini yuboring.");
    }

    public function list(): void
    {
        app(ListCommand::class)->handleList($this->chat, app(ChatTypeService::class), app(WorkerRoleService::class));
    }

    protected function handleChatMemberJoined(User $member): void
    {
        app(RegisterCommand::class)->handleRegister($this->chat, $member, app(ChatTypeService::class));
    }

    protected function handleChatMemberLeft(User $member): void
    {
        app(LeaveCommand::class)->handleLeave($this->chat, $member, app(ChatTypeService::class));
    }

    public function send_sms(): void
    {
        $this->reply("Salom");
    }
}
