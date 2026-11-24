<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ---- products: harga angka + minimal hari pemesanan ----
        Schema::table('products', function (Blueprint $t) {
            $t->unsignedInteger('price')->nullable();
            $t->unsignedTinyInteger('lead_days')->default(2);
        });
        foreach (DB::table('products')->get() as $p) {
            $price = null;
            if (preg_match('/^(\d+)k$/i', trim((string) $p->price_label), $m)) {
                $price = (int) $m[1] * 1000;
            }
            DB::table('products')->where('id', $p->id)->update([
                'price'     => $price,
                'lead_days' => stripos($p->name, 'birthday') !== false ? 5 : 2,
            ]);
        }

        // ---- orders: status, total, penanda baru ----
        Schema::table('orders', function (Blueprint $t) {
            $t->string('status')->default('menunggu');
            $t->unsignedBigInteger('total')->default(0);
            $t->timestamp('opened_at')->nullable();
        });
        // pesanan lama dianggap sudah dibuka
        DB::table('orders')->update(['opened_at' => DB::raw('created_at')]);

        // ---- order_items: isi pesanan ----
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->unsignedInteger('price')->nullable();
            $t->unsignedSmallInteger('qty');
            $t->unsignedBigInteger('subtotal')->default(0);
            $t->timestamps();
        });

        // ---- reviews: persetujuan admin ----
        Schema::table('reviews', function (Blueprint $t) {
            $t->string('status')->default('approved'); // komentar lama langsung tampil
        });
    }

    public function down(): void
    {
        Schema::table('reviews', fn (Blueprint $t) => $t->dropColumn('status'));
        Schema::dropIfExists('order_items');
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['status', 'total', 'opened_at']));
        Schema::table('products', fn (Blueprint $t) => $t->dropColumn(['price', 'lead_days']));
    }
};
