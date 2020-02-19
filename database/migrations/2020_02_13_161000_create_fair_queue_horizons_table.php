<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;

class CreateFairQueueHorizonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fair_queue_horizons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->string('uuid', 36)->index();
            $table->integer('memory_limit')->default(1024);
            $table->integer('trim_recent')->default(2880);
            $table->integer('trim_recent_failed')->default(2880);
            $table->integer('trim_failed')->default(10080);
            $table->integer('trim_monitored')->default(2880);
            $table->boolean('active')->index()->default(false);
            $table->string('ip')->index()->nullable();
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
        Schema::drop('fair_queue_horizons');
    }
}
