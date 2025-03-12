<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "order";
    public $timestamps = false;

    protected $fillable = [
        'totalPrice',
        'user_id',
        'paymentMethod',
        'note',
        'shipping_address',
        'shipping_status',
        'phoneNumber',
        'payment_status'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class,'order_id');
    }
}