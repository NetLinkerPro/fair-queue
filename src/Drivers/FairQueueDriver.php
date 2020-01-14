<?php

namespace Netlinker\FairQueue\Drivers;

use Illuminate\Queue\RedisQueue;
use Illuminate\Support\Facades\Queue;
use Netlinker\FairQueue\Models\FairIdentifier;
use Netlinker\FairQueue\Models\IdentifierModel;
use Netlinker\FairQueue\Models\ModelKey;
use Netlinker\FairQueue\Queues\QueueNameBuilder;

class FairQueueDriver extends RedisQueue
{


    /**
     * Push a new job onto the queue.
     *
     * @param object|string $job
     * @param mixed $data
     * @param string|null $queue
     * @return mixed
     * @throws \Netlinker\FairQueue\Exceptions\FairQueueException
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
     * @throws \Netlinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function size($queue = 'default')
    {
        $modelKey = ModelKey::get($queue);
        $maxId = IdentifierModel::maxId($modelKey);

        $size = 0;
        foreach (range(0, $maxId) as $number) {

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
     * @throws \Netlinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function pop($queue = 'default')
    {
        $modelKey = ModelKey::get($queue);
        return $this->findFairPop($modelKey, $queue);
    }

    /**
     * Find fair pop
     *
     * @param $modelKey
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \Netlinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findFairPop($modelKey, $queue = 'default')
    {
        while (Queue::size($queue) > 0 && !$res = $this->fairPop($modelKey, $queue)) {

        }
        return $res;
    }

    /**
     * Fair pop
     *
     * @param $modelKey
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \Netlinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function fairPop($modelKey, $queue = 'default')
    {
        $fairId = FairIdentifier::get($modelKey);
        $queueName = QueueNameBuilder::build($queue, $modelKey, $fairId);
        return parent::pop($queueName);
    }

}