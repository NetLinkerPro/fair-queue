<?php

namespace NetLinker\FairQueue\Drivers;

use Illuminate\Filesystem\Cache;
use Illuminate\Queue\RedisQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use NetLinker\FairQueue\Configuration\InstanceConfig;
use NetLinker\FairQueue\Models\FairIdentifier;
use NetLinker\FairQueue\Models\IdentifierModel;
use NetLinker\FairQueue\Models\ModelKey;
use NetLinker\FairQueue\Queues\QueueNameBuilder;

class FairQueueDriver extends RedisQueue
{


    /**
     * Push a new job onto the queue.
     *
     * @param object|string $job
     * @param mixed $data
     * @param string|null $queue
     * @return mixed
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function push($job, $data = '', $queue = 'default')
    {
        $modelKey = ModelKey::get($queue);
        $modelId = $job->modelId ?? 0;
        $queue = QueueNameBuilder::build($queue, $modelKey, $modelId);
        return parent::push($job, $data, $queue);
    }

    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     * @return int
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function size($queue = 'default')
    {
        if (Str::startsWith($queue, 'fair_queue:')){
            return parent::size($queue);
        }

        $modelKey = ModelKey::get($queue);
        $maxId = IdentifierModel::maxId($modelKey, $queue);

        $size = 0;
        foreach (range(0, $maxId) as $number) {

            if (!$this->isAllowPop($queue, $modelKey, $number)){
                continue;
            }

            $queueName = QueueNameBuilder::build($queue, $modelKey, $number);
            $size += parent::size($queueName);

        }
        return $size;
    }


    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function pop($queue = 'default')
    {
        InstanceConfig::detect();
        $modelKey = ModelKey::get($queue);
        return $this->findFairPop($modelKey, $queue);

    }

    /**
     * Find fair pop
     *
     * @param $modelKey
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findFairPop($modelKey, $queue = 'default')
    {
        $res = null;

        while(!$res){

            $fairId = FairIdentifier::get($modelKey, $queue);

            if (!$this->isAllowPop($queue, $modelKey, $fairId)){

                continue;
            }

            $queueName = QueueNameBuilder::build($queue, $modelKey, $fairId);
            $res = parent::pop($queueName);

            if (!$res && $this->isEmptyQueue($queue)){
                break;
            }

        }
        return $res;
    }

    /**
     * Is empty queue
     *
     * @param $queue
     * @return bool
     */
    private function isEmptyQueue($queue){
        return Queue::size($queue) <= 0;
    }

    /**
     * Is allow pop
     * @param string $queue
     * @param $modelKey
     * @param int $modelId
     * @return bool
     */
    private function isAllowPop(string $queue, $modelKey, int $modelId):bool
    {
        $instanceConfig = InstanceConfig::get();

        $active = Arr::get($instanceConfig, 'active');

        // not active queue in instance
        if (!$active){
            return false;
        }

        $modelConfig = Arr::get($instanceConfig, 'queues.' . QueueNameBuilder::buildOnlyName($modelKey, $queue) . '.' . $modelKey);

        // not set queue and model for this instance
        if (!$modelConfig){
            return false;
        }

        $activeModel = Arr::get($modelConfig, 'active');

        // not active model for this instance
        if (!$activeModel){
            return false;
        }

        $allowIds = Arr::get($modelConfig, 'allow_ids', []);
        $excludeIds = Arr::get($modelConfig, 'exclude_ids', []);

        if (!$allowIds && !$excludeIds){
            return true;
        }

        if (in_array($modelId, $allowIds)){
            return true;
        }

        if (in_array($modelId, $excludeIds)){
            return false;
        }

        return !!$excludeIds;
    }

}