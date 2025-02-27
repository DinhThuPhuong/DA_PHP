<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //Dat ten table
    protected $table = "order_detail";
    //Tat tu dong tang gia tri chyo khoa chinhchinh
    public $incrementing = false;

    
    public $timestamps = false;

    protected $fillable = [
        'quantity',
        'product_id',
        'order_id',
    ];
}