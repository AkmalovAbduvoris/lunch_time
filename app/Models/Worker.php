<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'telegram_id',
        'name',
        'username',
        'chat_id',
        'is_active',
    ];
}
