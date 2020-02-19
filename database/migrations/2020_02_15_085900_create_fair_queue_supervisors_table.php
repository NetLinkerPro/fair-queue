<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;

class CreateFairQueueSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fair_queue_supervisors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->string('uuid', 36)->index();
            $table->string('environment')->index();
            $table->string('connection')->index();
            $table->string('balance')->index()->default('false');
            $table->integer('min_processes')->default(1);
            $table->integer('max_processes')->default(1);
            $table->integer('priority')->default(0);
            $table->boolean('active')->index()->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fair_queue_supervisors');
    }
}
