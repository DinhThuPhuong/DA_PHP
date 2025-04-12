<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = "store";

    protected $fillable = [
        'storeName',
        'description',
        'avatar',
        'ownId',
        'status', 

    ];
    public function owner()
    {
        return $this->belongsTo(User::class, 'ownId', 'id');
    }
}