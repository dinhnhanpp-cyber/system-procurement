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
       Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên bộ quy tắc (Ví dụ: Công thức giá bán 2026)');
            $table->boolean('status')->default(1)->comment('Trạng thái: 1 = Hoạt động, 0 = Ngưng');
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
        Schema::dropIfExists('pricing_rules');
    }
};
