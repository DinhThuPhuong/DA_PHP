<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('order', function (Blueprint $table) {
            // Chọn kiểu dữ liệu phù hợp (decimal hoặc float), ví dụ:
            $table->decimal('total_amount', 15, 2)->after('store_id'); // Thêm cột total_amount sau store_id
        });
    }
    public function down(): void {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
    }
};
