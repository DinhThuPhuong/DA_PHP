<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreNotification extends Model
{
    protected $table = "store_notification";

    protected $fillable = [
        'store_id',
        'message',
        'type',
        'isRead',
        'user_id',
    ];
}