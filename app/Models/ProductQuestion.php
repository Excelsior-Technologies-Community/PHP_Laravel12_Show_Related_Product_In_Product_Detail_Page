<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuestion extends Model
{
    protected $fillable = ['product_id', 'user_id', 'customer_name', 'question', 'answer', 'is_approved'];

    protected $casts = ['is_approved' => 'boolean'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
