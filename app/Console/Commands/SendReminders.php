<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Worker;
use App\Jobs\SendPrivateMessage;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Har kuni foydalanuvchilarga eslatma xabari yuborish';

    public function handle()
    {
        $workers = Worker::all();

        foreach ($workers as $worker) {
            SendPrivateMessage::dispatch($worker);
        }

        $this->info('Xabarlar yuborilishga rejalashtirildi.');
    }
}
