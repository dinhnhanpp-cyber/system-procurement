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
    public function up()
    {
        Schema::create('price_sheet_item_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_sheet_item_id')
                ->constrained('price_sheet_items')
                ->onDelete('cascade');

            // Lưu ID quy tắc (nếu có) để biết mốc này lấy từ rule nào
            $table->foreignId('pricing_rule_detail_id')->nullable()->constrained('pricing_rule_details')->nullOnDelete();

            $table->decimal('margin_percent', 5, 2)->default(0);  // % Lợi nhuận (VD: 5.00, 10.00)
            $table->decimal('selling_price', 15, 2)->default(0);  // Giá bán tương ứng
            $table->decimal('profit', 15, 2)->default(0);         // Lợi nhuận tương ứng

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_sheet_item_results');
    }
};
