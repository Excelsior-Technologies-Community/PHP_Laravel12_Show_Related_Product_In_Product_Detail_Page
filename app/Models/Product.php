<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'brand',
        'price',
        'discount_price',
        'stock',
        'details',
        'tags',
        'image',
        'category_id',
        'status',
        'featured',
        'is_new_arrival',
        'sales_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function questions()
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getSellingPriceAttribute(): float
    {
        return (float) ($this->discount_price ?: $this->price);
    }

    public function getTagListAttribute(): array
    {
        return array_filter($this->tags ?? []);
    }
}