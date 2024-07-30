<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'name',
        'price',
        'quantity',
        'image',
    ];


    public function user()
    {
        return $this->belongsTo(UserBasic::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
