<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = ['product_id', 'user_id', 'customer_name', 'rating', 'review', 'is_verified_purchase', 'is_approved'];

    protected $casts = ['is_verified_purchase' => 'boolean', 'is_approved' => 'boolean'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
