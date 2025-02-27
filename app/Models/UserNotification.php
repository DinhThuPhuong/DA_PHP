<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = "user_notification";
    
    protected $fillable = [
        'user_id',
        'message',
        'type',
        'isRead',
        'store_id',
    ];
}