<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // In create_medicine_medicine_unit_table.php
    public function up()
    {
        Schema::create('medicine_medicine_unit', function (Blueprint $table) {
            $table->uuid('medicine_id');
            $table->uuid('medicine_unit_id');
            $table->primary(['medicine_id', 'medicine_unit_id']);
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('medicine_unit_id')->references('id')->on('medicine_units')->onDelete('cascade');
            $table->double('retail_price')->default(0.0); // Retail price
            $table->double('wholesale_price')->default(0.0); // Wholesale price
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_medicine_unit');
    }
};
