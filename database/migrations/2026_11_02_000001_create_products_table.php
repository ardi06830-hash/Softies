<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('category');            // cookie | brownie | custom
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->string('price_label');         // contoh: "5k", "12k"
            $table->string('badge')->nullable();   // contoh: "🔥 Bestseller"
            $table->string('emoji')->nullable();
            $table->boolean('is_featured')->default(false); // tampil di hero
            $table->string('featured_price')->nullable();   // contoh: "Rp 5k", "Rp 12k / pack"
            $table->unsignedSmallInteger('featured_order')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
