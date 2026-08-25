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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('internal_code', 50)->unique(); // Mã nội bộ (VD: URE-001)
            $table->string('short_name');                  // Tên dễ nhớ (VD: Ure)
            $table->string('international_name');          // Tên quốc tế (VD: Urea)
            $table->string('international_code', 50);      // Mã quốc tế (VD: UREA)
            $table->string('unit', 50);                    // ĐVT (VD: Tấn)
            $table->boolean('status')->default(true);      // Trạng thái (1: Hoạt động, 0: Ngưng)

            // Khóa ngoại liên kết với bảng product_categories
            $table->foreignId('category_id')
                  ->constrained('product_categories')
                  ->onDelete('cascade');

            // Khóa ngoại liên kết với bảng suppliers (Cho phép null nếu sản phẩm chưa gán NCC)
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->onDelete('set null');

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
        Schema::dropIfExists('products');
    }
};
