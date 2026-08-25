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
        Schema::create('price_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sheet_id')->constrained('price_sheets')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');

            // 1. Dữ liệu nhân viên gõ tay
            $table->decimal('ttl', 12, 2)->default(0);
            $table->decimal('fob', 12, 2)->default(0);
            $table->decimal('logistics', 15, 2)->default(0);
            $table->decimal('competitor_price', 12, 2)->nullable();

            // 2. Snapshot cấu hình chi phí (Auto-fill từ product_cost_settings, cho phép sửa)
            $table->decimal('import_tax', 5, 2)->default(0);    // %
            $table->decimal('vat', 5, 2)->default(0);           // %
            $table->decimal('service_percent', 5, 2)->default(0); // %
            $table->decimal('warehouse_percent', 5, 2)->default(0); // %
            $table->decimal('thc', 12, 2)->default(0);
            $table->decimal('do', 12, 2)->default(0);
            $table->decimal('cic', 12, 2)->default(0);
            $table->decimal('cleaning', 12, 2)->default(0);
            $table->decimal('lcc', 12, 2)->default(0);
            $table->decimal('operation', 12, 2)->default(0);

            // 3. Kết quả tính toán tự động
            $table->decimal('price_amount', 15, 2)->default(0);     // FOB * TTL
            $table->decimal('tax_amount', 15, 2)->default(0);       // Tiền thuế
            $table->decimal('service_amount', 15, 2)->default(0);   // Tiền service
            $table->decimal('warehouse_amount', 15, 2)->default(0); // Tiền kho
            $table->decimal('total_amount', 15, 2)->default(0);     // Tổng chi phí
            $table->decimal('cost_per_ton', 15, 2)->default(0);     // GIÁ VỐN / TẤN

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_sheet_items');
    }
};
