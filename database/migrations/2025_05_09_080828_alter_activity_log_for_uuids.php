<?php

// database/migrations/xxxx_xx_xx_xxxxxx_alter_activity_log_for_uuids.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn(['causer_id', 'subject_id']);
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->uuid('causer_id')->nullable()->after('id');
            $table->uuid('subject_id')->nullable()->after('causer_type');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn(['causer_id', 'subject_id']);
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
        });
    }
};

