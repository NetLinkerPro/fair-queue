<?php


namespace NetLinker\FairQueue\Models;


use Illuminate\Support\Facades\Cache;

class FairIdentifier
{

    /**
     * Get fair identifier
     *
     * @param $model
     * @return bool|int
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function get(string $model, string $queue = 'default'){

        $maxId = IdentifierModel::maxId($model, $queue);

        if (!$maxId){
            return 0;
        }

        $cacheKey = 'fair-queue:identifier:' .$queue. ':' . $model . ':current-id';

        $currentId = Cache::store(config('fair-queue.cache_store'))->increment($cacheKey);

        if ($currentId > $maxId){

            Cache::store(config('fair-queue.cache_store'))->forget($cacheKey);
            return 0;

        }

        return (int) $currentId;

    }
}