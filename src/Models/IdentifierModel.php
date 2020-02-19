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
     * @param string|null $model
     * @param string $queue
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function maxId(string $model, $queue = 'default')
    {
        $cacheKey = 'fair-queue:identifier:' . QueueNameBuilder::buildNameWithModelKey($model, $queue) . ':max-id';

        $maxId = Cache::store(config('fair-queue.cache_store'))->get($cacheKey);

        if (!$maxId) {
            $model = ModelSelect::select($model);
            $maxId = $model::max('id');

            $refreshMaxId = InstanceRefreshMaxId::get($model, $queue, 60);

            Cache::store(config('fair-queue.cache_store'))->put($cacheKey, $maxId, $refreshMaxId);
        }

        return (int)$maxId;

    }

}