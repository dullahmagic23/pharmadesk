<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_dosages_table.php
    public function up()
    {
        Schema::create('dosages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dose')->nullable();       // e.g., "1 tablet"
            $table->string('frequency')->nullable();  // e.g., "twice daily"
            $table->string('duration')->nullable();   // e.g., "5 days"
            $table->text('instructions')->nullable(); // e.g., "after meals"
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosages');
    }
};
