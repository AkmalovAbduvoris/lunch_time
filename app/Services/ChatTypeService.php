<?php

namespace App\Services;

use DefStudio\Telegraph\Models\TelegraphChat;

class ChatTypeService
{
    public function isGroup(TelegraphChat $chat): bool
    {
        return str_contains(strtolower($chat->title ?? ''), 'group') || $this->isChatIdGroup($chat);
    }

    public function isPrivate(TelegraphChat $chat): bool
    {
        return !$this->isGroup($chat);
    }

    private function isChatIdGroup(TelegraphChat $chat): bool
    {
        return str_starts_with((string) $chat->chat_id, '-');
    }
}
