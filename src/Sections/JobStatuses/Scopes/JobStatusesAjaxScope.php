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

        $builder = $builder->whereIn('status', [JobStatus::STATUS_EXECUTING, JobStatus::STATUS_QUEUED]);

        if ($value && !in_array('all', $value)){

            $builder = $builder->where(function ($query) use (&$value) {

                foreach ($value as $key => $queue){

                    if (!$key){
                        $query->where('queue', 'like', 'fair_queue:'.$queue.'%');
                    } else {
                        $query->orWhere('queue', 'like', 'fair_queue:'.$queue.'%');
                    }
                }
            });

        }

        return $builder->orderBy('created_at', 'asc');
    }
}