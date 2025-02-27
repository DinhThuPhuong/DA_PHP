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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('productName');
            $table->unsignedInteger('remainQuantity');
            $table->float('price');
            $table->foreignId('store_id')->constrained('store')->cascadeOnDelete();
            $table->string('thumnail');
            $table->boolean('isValidated')->default(false);
            $table->unsignedInteger('soldQuantity');
            $table->string('productDetail');

            
        
            
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_product');
    }
};