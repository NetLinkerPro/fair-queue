<?php


namespace Netlinker\FairQueue\Models;


use Illuminate\Support\Facades\Cache;

class FairIdentifier
{

    /**
     * Get fair identifier
     *
     * @param $modelKey
     * @return bool|int
     * @throws \Netlinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function get(string $modelKey, string $queue = 'default'){

        $maxId = IdentifierModel::maxId($modelKey, $queue);

        if (!$maxId){
            return 0;
        }

        $cacheKey = 'fair-queue:identifier:' .$queue. ':' . $modelKey . ':current-id';

        $currentId = Cache::store(config('fair-queue.cache_store'))->increment($cacheKey);

        if ($currentId > $maxId){

            Cache::store(config('fair-queue.cache_store'))->forget($cacheKey);
            return 0;

        }

        return $currentId;

    }
}