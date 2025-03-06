<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageDetail extends Model
{
    protected $table = "images_detail";
    public $timestamps = false;


    protected $fillable = [
        'imageUrl',
        'product_id',
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
