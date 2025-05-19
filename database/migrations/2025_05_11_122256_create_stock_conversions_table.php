<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migration for stock_conversions table
        Schema::create('stock_conversions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('from_unit_id');
            $table->foreignUuid('to_unit_id');
            $table->integer('conversion_factor');
            $table->integer('quantity_converted');
            $table->integer('stock_generated');
            $table->foreignUuid('user_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_conversions');
    }
};
