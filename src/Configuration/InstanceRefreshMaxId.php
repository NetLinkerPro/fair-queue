<?php


namespace NetLinker\FairQueue\Configuration;


use Illuminate\Support\Arr;
use NetLinker\FairQueue\Queues\QueueNameBuilder;

class InstanceRefreshMaxId
{
    /**
     * Get refresh max ID
     *
     * @param string $modelKey
     * @param string $queue
     * @param int $default
     * @return mixed
     */
    public static function get(string $modelKey, string $queue='default', int $default = 60){
        $instanceConfig = InstanceConfig::get();
        return Arr::get($instanceConfig, 'queues.' . QueueNameBuilder::buildOnlyName($modelKey, $queue) . '.' . $modelKey . '.refresh_max_id', $default);
    }
}