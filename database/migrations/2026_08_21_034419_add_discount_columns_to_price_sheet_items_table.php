<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('price_sheet_items', function (Blueprint $table) {
            // Thêm các cột mới (đặt sau cột competitor_price nếu có)
            $table->decimal('competitor_discounted_price', 15, 2)->nullable()->after('competitor_price');
            $table->decimal('discount_percent', 5, 2)->default(10)->after('competitor_discounted_price');
        });
    }

    public function down(): void
    {
        Schema::table('price_sheet_items', function (Blueprint $table) {
            $table->dropColumn(['competitor_discounted_price', 'discount_percent']);
        });
    }
};
