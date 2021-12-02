<?php


namespace NetLinker\FairQueue;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use NetLinker\FairQueue\Queues\QueueConfiguration;

class SystemWorkLogger
{

    /**
     * Log working
     *
     * @param $queue
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function log($queue)
    {
        /** @var QueueConfiguration $config */
        $config = app()->make(QueueConfiguration::class);

        if (!$config->horizon){
            return;
        }
        $horizonUuid = $config->horizon->uuid;
        $supervisorUuid = $config->supervisor->uuid;
        $queueUuid = $config->queues[$queue]->uuid;

        $key = sprintf('fair-queue.horizons.last_work_logged_at.' . $horizonUuid);
        Cache::store(config('fair-queue.cache_store'))->set($key, now());

        $key = sprintf('fair-queue.supervisors.last_work_logged_at.' . $supervisorUuid);
        Cache::store(config('fair-queue.cache_store'))->set($key, now());

        $key = sprintf('fair-queue.queues.last_work_logged_at.' . $queueUuid);
        Cache::store(config('fair-queue.cache_store'))->set($key, now());

    }

    /**
     * Get horizon last work logged at
     *
     * @param $queue
     * @return Carbon|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function getHorizonLastWorkLoggedAt($uuid){
        $key = sprintf('fair-queue.horizons.last_work_logged_at.' . $uuid);
        return Cache::store(config('fair-queue.cache_store'))->get($key);
    }

    /**
     * Get supervisor last work logged at
     *
     * @param $queue
     * @return Carbon|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function getSupervisorLastWorkLoggedAt($uuid){
        $key = sprintf('fair-queue.supervisors.last_work_logged_at.' . $uuid);
        return Cache::store(config('fair-queue.cache_store'))->get($key);
    }

    /**
     * Get queue last work logged at
     *
     * @param $queue
     * @return Carbon|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function getQueueLastWorkLoggedAt($uuid){
        $key = sprintf('fair-queue.queues.last_work_logged_at.' . $uuid);
        return Cache::store(config('fair-queue.cache_store'))->get($key);
    }
}