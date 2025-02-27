<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $table = "follower";

    //Tat tinh nang tu dong tang khoa chinhchinh
    public $incrementing = false;

    
    public $timestamps = true;

    
    protected $fillable = [
        'store_id',
        'user_id',
    ];
}