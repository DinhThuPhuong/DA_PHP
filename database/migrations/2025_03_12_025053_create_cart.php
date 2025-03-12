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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');     // Liên kết tới bảng users
            $table->unsignedBigInteger('product_id');  // Liên kết tới bảng products
            $table->integer('quantity')->default(1);   // Số lượng sản phẩm
            $table->timestamps();

            // Thiết lập khóa ngoại để đảm bảo dữ liệu liên kết
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};