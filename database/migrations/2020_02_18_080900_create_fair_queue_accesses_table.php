<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;

class CreateFairQueueAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fair_queue_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36)->index();
            $table->string('queue_uuid', 36)->index();
            $table->string('name')->index();
            $table->text('description')->nullable();

            $table->string('type')->index();
            $table->string('object_uuid', 36)->index();

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
        Schema::drop('fair_queue_accesses');
    }
}
