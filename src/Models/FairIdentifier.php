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
    public static function get($modelKey){

        $maxId = IdentifierModel::maxId($modelKey);

        if (!$maxId){
            return 0;
        }

        $cacheKey = 'fair-queue:' . $modelKey . ':current-id';

        $currentId = Cache::increment($cacheKey);

        if ($currentId > $maxId){

            Cache::forget($cacheKey);
            return 0;

        }

        return $currentId;

    }
}