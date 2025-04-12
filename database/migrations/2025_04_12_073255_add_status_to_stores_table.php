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
        Schema::table('store', function (Blueprint $table) {
            // Thêm cột status với các giá trị có thể có và mặc định là 'pending'
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('ownId');
            // Thêm index cho status để tăng tốc độ truy vấn (tùy chọn)
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store', function (Blueprint $table) {
            // Xóa cột status nếu rollback migration
            $table->dropColumn('status');
            // Xóa index nếu bạn đã thêm nó
            // $table->dropIndex(['status']); // Tên index có thể khác
        });
    }
};