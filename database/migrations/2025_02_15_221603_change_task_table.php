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
        Schema::table(TBL_TASK, function (Blueprint $table) {
            $table->tinyInteger('duration_plan')->unsigned()->default(30)->change();
            $table->tinyInteger('duration_fact')->unsigned()->nullable()->change();
            $table->date('dt_closed')->nullable()->change();
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
