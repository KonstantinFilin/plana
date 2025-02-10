<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Task;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TBL_TASK, function (Blueprint $table) {
            $table->id();
            $table->smallInteger("group_id")->unsigned()->nullable();
            $table->string("short");
            $table->text("description")->nullable();
            $table->smallInteger("duration_plan")->unsigned()->default(15);
            $table->smallInteger("duration_fact")->unsigned()->nullable();
            $table->enum("priority", Task::priorityList())->default(Task::PRIORITY_B);
            $table->smallInteger("sum_paid")->unsigned()->default(0);
            $table->smallInteger("sum_rest")->unsigned()->default(0);
            $table->date("plan_dt")->nullable();
            $table->time("plan_time")->nullable();
            $table->time("dt_closed")->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TBL_TASK);
    }
};
