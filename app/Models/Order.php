<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Order extends Model
{
    use HasFactory, Notifiable;

    protected $table = "order";
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'store_id',
        'total_amount',
        // Bỏ 'shipping_address' dạng JSON
        'phoneNumber',
        'note',
        'shipping_status',
        'paymentMethod',
        'payment_status',
        // Thêm các cột địa chỉ mới (dùng đúng tên cột snake_case trong DB nếu bạn đặt theo convention)
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_country',
    ];

     protected $casts = [
        // Bỏ 'shipping_address' => 'array',
        'payment_status' => 'boolean',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class,'order_id', 'id');
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }

     public function store()
     {
          return $this->belongsTo(Store::class, 'store_id');
     }
}
