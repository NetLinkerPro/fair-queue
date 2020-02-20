<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;

class CreateFairQueueJobStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fair_queue_job_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('owner_uuid', 36)->index();
            $table->string('name')->index()->nullable();
            $table->string('uuid', 36)->index();
            $table->string('job_id')->index()->nullable();
            $table->string('type')->index();
            $table->boolean('interrupt')->default(false);
            $table->string('external_uuid', 36)->index()->nullable();
            $table->string('horizon_uuid', 36)->index()->nullable();
            $table->string('queue')->index()->nullable();
            $table->integer('attempts')->default(0);
            $table->integer('progress_now')->default(0);
            $table->integer('progress_max')->default(0);
            $table->string('status', 16)->default(JobStatus::STATUS_QUEUED)->index();
            $table->longText('input')->nullable();
            $table->longText('logs')->nullable();
            $table->longText('output')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
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
        Schema::drop('fair_queue_job_statuses');
    }
}
