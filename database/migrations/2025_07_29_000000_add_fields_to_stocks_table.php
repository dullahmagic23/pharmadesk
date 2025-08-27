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
        Schema::table('stocks', function (Blueprint $table) {
            // Indexes
            $table->index(['stockable_id', 'stockable_type']);
            $table->index('unit_id');

            // Foreign key constraint for unit_id
            $table->foreign('unit_id')->references('id')->on('medicine_units')->onDelete('set null');

            // New fields
            $table->string('status')->default('available')->after('unit_id');
            $table->date('expiration_date')->nullable()->after('status');
            $table->string('batch_number')->nullable()->after('expiration_date');
            $table->uuid('location_id')->nullable()->after('batch_number');
            $table->softDeletes();
            $table->uuid('created_by')->nullable()->after('location_id');
            $table->uuid('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex(['stockable_id', 'stockable_type']);
            $table->dropIndex(['unit_id']);
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['status', 'expiration_date', 'batch_number', 'location_id', 'deleted_at', 'created_by', 'updated_by']);
        });
    }
};
