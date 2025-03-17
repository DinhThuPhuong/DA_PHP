<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users');
        $table->foreignId('store_id')->constrained('store');
        $table->string('shipping_address');
        $table->string('phoneNumber');
        $table->string('note')->nullable();
        $table->string('paymentMethod');
        $table->string('payment_status');
        $table->string('shipping_status');
        $table->decimal('totalPrice', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};