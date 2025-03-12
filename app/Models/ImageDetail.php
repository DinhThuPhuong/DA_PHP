<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageDetail extends Model
{
    protected $table = "images_detail";
    public $timestamps = false;

    
    protected $fillable = [
        'imageUrl',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}