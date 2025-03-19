<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     //Dat ten cho table trong csdlcsdl
     protected $table = 'product';

     
     public $timestamps = true;
 
     
     protected $fillable = [
         'category_id',
         'productName',
         'remainQuantity',
         'price',
         'store_id',
         'thumnail',
         'isValidated',
         'soldQuantity',
         'productDetail',
     ];

     public function imageDetails()
     {
         // Giả sử bảng imageDetail có khóa ngoại là product_id
         return $this->hasMany(ImageDetail::class, 'product_id');
     }
}