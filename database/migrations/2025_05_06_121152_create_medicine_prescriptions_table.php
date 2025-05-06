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
        Schema::create('medicine_prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('prescription_id')
                ->constrained('prescriptions')
                ->onDelete('cascade');

            $table->foreignUuid('medicine_id')
                ->constrained('medicines')
                ->onDelete('cascade');

            $table->foreignUuid('dosage_id')
                ->nullable()
                ->constrained('dosages') // explicit for clarity
                ->onDelete('set null');
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_prescriptions');
    }
};
