<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "message";

    protected $timestamp = true;

    protected $fillable = [
        'user_id',
        'store_id',
        'content',
        'isRead',
        'senderType',
    ];
}