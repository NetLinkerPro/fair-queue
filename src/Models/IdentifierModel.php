<?php


namespace NetLinker\FairQueue\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Netlinker\FairQueue\Cache\CacheKeyBuilder;
use NetLinker\FairQueue\Configuration\InstanceConfig;
use NetLinker\FairQueue\Configuration\InstanceRefreshMaxId;
use NetLinker\FairQueue\Queues\QueueNameBuilder;

class IdentifierModel
{
    /**
     * Max ID
     *
     * @param string|null $modelKey
     * @param string $queue
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function maxId(string $modelKey, string $queue = 'default')
    {
        $cacheKey = 'fair-queue:identifier:' . QueueNameBuilder::buildNameWithModelKey($modelKey, $queue) . ':max-id';

        $maxId = Cache::store(config('fair-queue.cache_store'))->get($cacheKey);

        if (!$maxId) {
            $model = ModelSelect::select($modelKey);
            $maxId = $model::max('id');

            $refreshMaxId = InstanceRefreshMaxId::get($modelKey, $queue, 60);

            Cache::store(config('fair-queue.cache_store'))->put($cacheKey, $maxId, $refreshMaxId);
        }

        return $maxId;

    }

}