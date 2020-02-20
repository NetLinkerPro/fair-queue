<?php


namespace NetLinker\FairQueue\Models;

use Illuminate\Support\Facades\Cache;
use NetLinker\FairQueue\Queues\QueueConfiguration;
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function maxId(string $model, $queue = null)
    {
        $queue = $queue ?? config('fair-queue.default_queue');

        $cacheKey = 'fair-queue:identifier:' . QueueNameBuilder::buildNameWithModelKey($model, $queue) . ':max-id';

        $maxId = Cache::store(config('fair-queue.cache_store'))->get($cacheKey);

        if (!$maxId) {
            $model = ModelSelect::select($model);
            $maxId = $model::max('id');

            $refreshMaxModelId = QueueConfiguration::getRefreshMaxModelId($queue);

            Cache::store(config('fair-queue.cache_store'))->put($cacheKey, $maxId, $refreshMaxModelId);
        }

        return (int)$maxId;

    }

}