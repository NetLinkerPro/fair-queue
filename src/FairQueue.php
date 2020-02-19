<?php

namespace NetLinker\FairQueue;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use NetLinker\FairQueue\Exceptions\FairQueueException;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;
use NetLinker\FairQueue\Sections\Horizons\Repositories\HorizonRepository;

class FairQueue
{

    /**
     * Get model from queue
     *
     * @param $queue
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getModelFromQueue($queue = 'default'){

        $queueParts = explode(':', $queue);

        // get model from queue
        $model = (sizeof($queueParts)> 1) ? $queueParts[1] : null;

        // get default model
        if (!$model){
            $model =  config('fair-queue.default_model');
        }

        return $model;
    }

    /**
     * Get class model by queue
     *
     * @param $queue
     * @return \Illuminate\Config\Repository|mixed
     * @throws FairQueueException
     */
    public function getClassModelByQueue($queue){
        $model = $this->getModelFromQueue($queue);
        return $this->getClassModel($model);
    }
    /**
     * Get class model
     *
     * @param $model
     * @return \Illuminate\Config\Repository|mixed
     * @throws FairQueueException
     */
    public function getClassModel($model){
        $class = config('fair-queue.models.' . $model);

        if (!$class){
            throw new FairQueueException(__('fair-queue::queues.exception_not_found_model'));
        }

        return $class;
    }

    /**
     * Get horizon
     *
     * @return Horizon|null
     */
    public function getHorizon(){
        $horizonUuid = config('fair-queue.horizon_uuid');
        return (new HorizonRepository())->findWhere(['uuid' => $horizonUuid])->first();
    }

}
