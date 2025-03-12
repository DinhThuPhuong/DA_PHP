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
            $table->text('note')->nullable()->after('isValidated'); // Ghi chú khách hàng
            $table->string('phoneNumber', 10)->after('note');
            $table->text('shipping_address')->after('note'); // Địa chỉ giao hàng
            $table->string('shipping_status')->default('Processing')->after('shipping_address'); // Trạng thái giao hàng
            $table->string('payment_status')->default('Pending')->after('shipping_status'); // Trạng thái thanh toán
            $table->string('paymentMethod')->default('COD')->after('payment_status'); // Phương thức thanh toán
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};