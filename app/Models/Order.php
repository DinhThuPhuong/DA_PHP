<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "order";
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'store_id',
        'total_amount', // Không phải totalPrice
        'shipping_address',
        'phone_number', // Không phải phoneNumber
        'note',
        'status', // Không phải shipping_status
        'payment_method', // Không phải paymentMethod
        'payment_status'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class,'order_id');
    }
}