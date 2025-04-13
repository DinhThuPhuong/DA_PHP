<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            // Thay đổi kiểu dữ liệu cột thành TEXT
            $table->text('shipping_address')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            // Quay lại kiểu VARCHAR nếu cần rollback (điều chỉnh độ dài nếu trước đó khác 255)
             // Cẩn thận vì rollback có thể mất dữ liệu nếu JSON đã dài hơn 255
            $table->string('shipping_address', 255)->change();
        });
    }
};
