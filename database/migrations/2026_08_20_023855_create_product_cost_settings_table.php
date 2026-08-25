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
        Schema::create('product_cost_settings', function (Blueprint $table) {
            $table->id();
            
            // Khóa ngoại liên kết bảng products
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Các tỷ lệ phần trăm (%)
            $table->decimal('import_tax', 5, 2)->default(0.00)->comment('Thuế nhập khẩu %');
            $table->decimal('vat', 5, 2)->default(0.00)->comment('Thuế VAT %');
            $table->decimal('service_percent', 5, 2)->default(3.00)->comment('Service %');
            $table->decimal('warehouse_percent', 5, 2)->default(1.00)->comment('Kho %');

            // Các khoản phí phụ thuộc vùng AA1:AA4
            $table->decimal('thc', 12, 2)->default(145.00);
            $table->decimal('do', 12, 2)->default(45.00);
            $table->decimal('cic', 12, 2)->default(50.00);
            $table->decimal('cleaning', 12, 2)->default(10.00);

            // LCC = Tổng phí * 1.08 (Tương đương SUM * 8% + SUM)
            $table->decimal('lcc', 12, 2)
                  ->storedAs('(thc + `do` + cic + cleaning) * 1.08')
                  ->comment('LCC = (THC + D/O + CIC + CLEANING) * 1.08');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_cost_settings');
    }
};
