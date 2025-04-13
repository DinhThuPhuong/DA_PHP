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
            // Xóa cột cũ lưu JSON (nếu đã đổi thành TEXT)
            // Nếu cột cũ vẫn là VARCHAR, bạn có thể không cần xóa ngay nếu muốn giữ lại tạm thời
             if (Schema::hasColumn('order', 'shipping_address')) {
                $table->dropColumn('shipping_address');
             }

            // Thêm các cột mới sau cột total_amount (ví dụ)
            // Đặt tên theo snake_case cho nhất quán với Laravel convention
            $table->string('shipping_first_name', 255)->nullable()->after('total_amount');
            $table->string('shipping_last_name', 255)->nullable()->after('shipping_first_name');
            $table->string('shipping_email', 255)->nullable()->after('shipping_last_name');
            $table->string('shipping_street', 255)->nullable()->after('shipping_email');
            $table->string('shipping_city', 255)->nullable()->after('shipping_street');
            $table->string('shipping_state', 255)->nullable()->after('shipping_city');
            $table->string('shipping_zipcode', 20)->nullable()->after('shipping_state');
            $table->string('shipping_country', 255)->nullable()->after('shipping_zipcode');
            // Cột phoneNumber đã có riêng rồi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            // Thêm lại cột cũ nếu cần rollback
             $table->text('shipping_address')->nullable()->after('total_amount');

             // Xóa các cột mới đã thêm
             $table->dropColumn([
                 'shipping_first_name',
                 'shipping_last_name',
                 'shipping_email',
                 'shipping_street',
                 'shipping_city',
                 'shipping_state',
                 'shipping_zipcode',
                 'shipping_country',
             ]);
        });
    }
};