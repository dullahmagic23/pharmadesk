<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
            $table->string('subject_type')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable(false)->change();
            $table->string('subject_type')->nullable(false)->change();
        });
    }
};
