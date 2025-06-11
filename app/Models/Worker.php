<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Worker extends Model
{
    use HasRoles;

    protected $guard_name = 'bot';

    protected $fillable = [
        'telegram_id',
        'name',
        'username',
        'chat_id',
        'is_active',
    ];
}
