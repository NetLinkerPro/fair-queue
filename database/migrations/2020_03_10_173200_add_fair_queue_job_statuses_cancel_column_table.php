<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFairQueueJobStatusesCancelColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fair_queue_job_statuses', function (Blueprint $table) {
            $table->boolean('cancel')->index()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fair_queue_job_statuses', function (Blueprint $table) {
            $table->dropColumn('cancel');
        });
    }
}
