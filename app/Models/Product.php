<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
     //Dat ten cho table trong csdlcsdl
     protected $table = 'product';

     
     public $timestamps = false;
 
     
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
     public function images(): HasMany {
        return $this->hasMany(ImageDetail::class, 'product_id');
    }
}