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
        Schema::create('store_notification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('store')->cascadeOnDelete();
            $table->string('message');
            $table->string('type');
            $table->boolean('isRead')->default(false);
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_notification');
    }
};