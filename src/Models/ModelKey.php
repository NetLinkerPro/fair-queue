<?php


namespace NetLinker\FairQueue\Models;


use NetLinker\FairQueue\Exceptions\FairQueueException;

class ModelKey
{

    /**
     * Get model key
     *
     * @param null $queue
     * @return \Illuminate\Config\Repository|mixed
     * @throws FairQueueException
     */
    public static function get($queue = null){

        if ($queue === null){
            return config('fair-queue.default_model');
        }

        $explodeQueue = explode(':', $queue);

        if (sizeof($explodeQueue) < 2){

            return config('fair-queue.default_model');

        } else if (sizeof($explodeQueue) === 2){

            $modelKey = end($explodeQueue);

        } else if (sizeof($explodeQueue) === 4){

            $modelKey = $explodeQueue[2];

        }

        if (!config('fair-queue.models.'.$modelKey)){
            throw new FairQueueException('Not found model key in config `fair-queue.models`');
        }

        return $modelKey;

    }
}