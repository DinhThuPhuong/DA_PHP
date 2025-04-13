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
            // Xóa cột totalPrice không cần thiết
            $table->dropColumn('totalPrice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            // Thêm lại cột nếu cần rollback (chọn kiểu dữ liệu phù hợp)
            $table->decimal('totalPrice', 15, 2)->nullable(); // Hoặc kiểu dữ liệu gốc của nó
        });
    }
};
