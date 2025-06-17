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
use App\Models\Worker;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

use function PHPUnit\Framework\callback;

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

    public function move_up(): void
    {
        $id = $this->data->get('id');
        $worker = Worker::find($id);

        if (!$worker) return;

        $above = Worker::where('order', '<', $worker->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($above) {
            [$worker->order, $above->order] = [$above->order, $worker->order];
            $worker->save();
            $above->save();
        }

        $this->updateWorkersList();
    }

    public function move_down(): void
    {
        $id = $this->data->get('id');
        $worker = Worker::find($id);

        if (!$worker) return;

        $below = Worker::where('order', '>', $worker->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($below) {
            [$worker->order, $below->order] = [$below->order, $worker->order];
            $worker->save();
            $below->save();
        }

        $this->updateWorkersList();
    }

    private function updateWorkersList(): void
    {
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
                Button::make("{$position}. {$workerItem->name}", callback('noop')),
            ];

            if ($index > 0) {
                $row[] = Button::make("⬆️")->action('move_up')->param('id', $workerItem->id);
            }

            if ($index < $workers->count() - 1) {
                $row[] = Button::make("⬇️")->action('move_down')->param('id', $workerItem->id);
            }
            $keyboard->row($row);
        }

        $this->chat->edit($this->callbackQuery->message()->id())
            ->html($message)
            ->keyboard($keyboard)
            ->send();
    }

}
