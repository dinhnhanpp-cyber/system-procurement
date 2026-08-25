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
        Schema::create('product_categories', function (Blueprint $table) {
        $table->id();
        // Tên loại sản phẩm
        $table->string('name');
        // Mã loại sản phẩm
        $table->string('code', 50)->unique();
        // Mô tả
        $table->text('description')->nullable();
        // 1 = đang sử dụng, 0 = ngưng sử dụng
        $table->boolean('status')->default(true);
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
        Schema::dropIfExists('product_categories');
    }
};
