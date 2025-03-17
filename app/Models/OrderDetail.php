<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //Dat ten table
    protected $table = "order_details";
    //Tat tu dong tang gia tri chyo khoa chinhchinh
    public $incrementing = false;

    
    public $timestamps = false;

    protected $fillable = [
        'quantity',
        'product_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}