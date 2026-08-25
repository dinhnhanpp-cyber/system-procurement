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
        Schema::create('pricing_rule_details', function (Blueprint $table) {
            $table->id();
            
            // Khóa ngoại liên kết đến bảng pricing_rules (Xóa mẹ tự động xóa con)
            $table->foreignId('rule_id')->constrained('pricing_rules')->onDelete('cascade');
            
            $table->string('type')->comment('Loại quy tắc: profit, discount, tax...');
            $table->string('name')->comment('Tên hiển thị (VD: Giá bán 5%, Discount đối thủ)');
            $table->decimal('value', 8, 2)->comment('Giá trị % hoặc số tiền');
            
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
        Schema::dropIfExists('pricing_rule_details');
    }
};
