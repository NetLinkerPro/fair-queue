<?php


namespace Netlinker\FairQueue\Models;

use Illuminate\Support\Facades\Cache;

class IdentifierModel
{
    /**
     * Max ID
     *
     * @param string|null $modelKey
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function maxId(string $modelKey)
    {

        $cacheKey = 'fair-queue:identifier:' . $modelKey . ':max-id';

        $maxId = Cache::store(config('fair-queue.cache_store'))->get($cacheKey);

        if (!$maxId) {
            $model = ModelSelect::select($modelKey);
            $maxId = $model::max('id');
            Cache::store(config('fair-queue.cache_store'))->put($cacheKey, $maxId, 60);
        }

        return $maxId;

    }

}