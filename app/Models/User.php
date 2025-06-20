<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'username',
        'telegram_id',
        'chat_id',
        'role',
    ];

    public $timestamps = true;
}
