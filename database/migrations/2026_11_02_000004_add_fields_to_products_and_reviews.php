<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->string('hero_name')->nullable();
            $table->string('hero_desc')->nullable();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('highlight')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'is_premium', 'hero_name', 'hero_desc']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('highlight');
        });
    }
};
