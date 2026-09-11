<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('slug');
            $table->string('brand')->nullable()->after('sku');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->unsignedInteger('stock')->default(0)->after('discount_price');
            $table->json('tags')->nullable()->after('details');
            $table->boolean('featured')->default(false)->after('status');
            $table->boolean('is_new_arrival')->default(false)->after('featured');
            $table->unsignedInteger('sales_count')->default(0)->after('is_new_arrival');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku', 'brand', 'discount_price', 'stock', 'tags',
                'featured', 'is_new_arrival', 'sales_count',
            ]);
        });
    }
};
