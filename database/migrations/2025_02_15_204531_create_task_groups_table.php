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
        Schema::create(TBL_TASK_GROUP, function (Blueprint $table) {
            $table->tinyIncrements('id')->unsigned();
            $table->tinyInteger('pid')->unsigned()->nullable();
            $table->string('name');
            $table->string('abbr', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TBL_TASK_GROUP);
    }
};
