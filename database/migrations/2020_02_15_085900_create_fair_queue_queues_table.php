<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;

class CreateFairQueueQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fair_queue_queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->string('uuid', 36)->index();
            $table->string('horizon_uuid', 36)->index();
            $table->string('supervisor_uuid', 36)->index();
            $table->string('queue')->index();
            $table->boolean('active')->index()->default(false);
            $table->integer('refresh_max_model_id')->default(60);
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
        Schema::drop('fair_queue_queues');
    }
}
