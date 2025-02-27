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
        Schema::create('order_details', function (Blueprint $table) {
            $table->unsignedInteger('quantity');
        
            $table->foreignId('product_id')
                  ->constrained('product')
                  ->cascadeOnDelete();
        
            $table->foreignId('order_id')
                  ->constrained('order')
                  ->cascadeOnDelete();
        
            $table->primary(['product_id', 'order_id']);
        
            // $table->timestamps(); // Thêm nếu cần
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