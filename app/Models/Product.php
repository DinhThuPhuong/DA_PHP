<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}