<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Scopes;

use AwesIO\Repository\Scopes\ScopeAbstract;
use NetLinker\FairQueue\Sections\JobStatuses\Models\JobStatus;

class JobStatusesAjaxScope extends ScopeAbstract
{
    /**
     * Scope
     *
     * @param $builder
     * @param $value
     * @param $scope
     * @return mixed
     */
    public function scope($builder, $value, $scope)
    {

        $builder = $builder->where(function ($query) use (&$value) {

            $query->whereIn('status', [JobStatus::STATUS_EXECUTING, JobStatus::STATUS_QUEUED]);

             $query->orWhere(function ($query) use (&$value) {

                 $maxDateSuccess = now()->subSeconds(config('fair-queue.job_statuses_show_success_ajax'));
                 $query->Where('status', JobStatus::STATUS_FINISHED)->where('finished_at', '>',$maxDateSuccess);

             });

            $query->orWhere(function ($query) use (&$value) {

                $maxDateFailed = now()->subSeconds(config('fair-queue.job_statuses_show_failed_ajax'));
                $query->Where('status', JobStatus::STATUS_FAILED)->where('finished_at', '>',$maxDateFailed);

            });

        });

        if ($value && !in_array('all', $value)) {

            $builder = $builder->where(function ($query) use (&$value) {

                foreach ($value as $key => $queue) {

                    if (!$key) {
                        $query->where('queue', 'like', 'fair_queue:' . $queue . '%');
                    } else {
                        $query->orWhere('queue', 'like', 'fair_queue:' . $queue . '%');
                    }
                }
            });

        }

        return $builder->orderBy('created_at', 'desc');
    }
}