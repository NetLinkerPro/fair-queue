<?php

namespace NetLinker\FairQueue\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Laravel\Horizon\Events\JobReserved;
use Laravel\Horizon\RedisQueue;
use NetLinker\FairQueue\Events\QueuePopping;
use NetLinker\FairQueue\Events\QueueFairJobFinding;
use NetLinker\FairQueue\Facades\FairQueue;
use NetLinker\FairQueue\HorizonManager;
use NetLinker\FairQueue\Models\FairIdentifier;
use NetLinker\FairQueue\Models\IdentifierModel;
use NetLinker\FairQueue\Models\ModelKey;
use NetLinker\FairQueue\Queues\QueueConfiguration;
use NetLinker\FairQueue\Queues\QueueNameBuilder;
use NetLinker\FairQueue\SystemWorkLogger;

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
    public function push($job, $data = '', $queue = null)
    {
        $queue = $queue ?? config('fair-queue.default_queue');

        $model = ModelKey::get($queue);
        $size = $this->incrementQueueSize($queue, $model);

        $modelId = $job->modelId ?? 0;
        $queue = QueueNameBuilder::build($queue, $model, $modelId);

        return parent::push($job, $data, $queue);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function pop($queue = null)
    {

        event(new QueuePopping($queue));

        $model = FairQueue::getModelFromQueue($queue);

        $job = $this->findFairJob($model, $queue);

        return tap($job, function ($result) use ($queue) {
            if ($result) {
                $this->event($this->getQueue($queue), new JobReserved($result->getReservedJob()));
            }
        });
    }

    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     * @return int
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function size($queue = null)
    {
        $queue = $queue ?? config('fair-queue.default_queue');

        if (Str::startsWith($queue, 'fair_queue:')) {
            return parent::size($queue);
        }

        $modelKey = ModelKey::get($queue);
        $maxId = IdentifierModel::maxId($modelKey, $queue);

        $size = 0;
        foreach (range(0, $maxId) as $number) {

            if (!$this->isAllowPop($number, $queue)) {
                continue;
            }

            $queueName = QueueNameBuilder::build($queue, $modelKey, $number);
            $size += parent::size($queueName);

        }
        return $size;
    }

    /**
     * Find fair job
     *
     * @param $model
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findFairJob($model, $queue = null)
    {
        $queue = $queue ?? config('fair-queue.default_queue');

        $res = null;

        while (!$res) {

            event(new QueueFairJobFinding($queue));

            $fairId = FairIdentifier::get($model, $queue);

            if (!$this->isAllowPop($fairId, $queue)) {

                if ($fairId === IdentifierModel::maxId($model, $queue)) {
                    break;
                }
                continue;
            }

            $queueName = QueueNameBuilder::build($queue, $model, $fairId);

            $res = parent::pop($queueName);

            $isEmptyQueue = $this->queueSize($queue, $model) <= 0;

            if (!$res && $isEmptyQueue) {
                break;
            } elseif ($res) {
                $this->decrementQueueSize($queue, $model) <= 0;
                break;
            }

        }
        return $res;
    }

    /**
     * Is allow pop
     * @param int $modelId
     * @param $queue
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function isAllowPop(int $modelId, $queue): bool
    {

        if (!QueueConfiguration::isQueueActive($queue)) {
            return null;
        }

        $allowAccesses = QueueConfiguration::getAllowAccesses($queue);
        $excludeAccesses = QueueConfiguration::getExcludeAccesses($queue);

        if (!$allowAccesses && !$excludeAccesses) {
            return true;
        }

        if (in_array($modelId, $allowAccesses)) {
            return true;
        }

        if (in_array($modelId, $excludeAccesses)) {
            return false;
        }

        return !!$excludeAccesses;
    }


    /**
     * Increment queue size
     *
     * @param string $queue
     * @param string $model
     * @return bool|int
     */
    private function incrementQueueSize(string $queue, string $model)
    {
        $queueWithModel = QueueNameBuilder::buildNameWithModelKey($model, $queue);
        $key = sprintf('fair-queue.queue_size.%1$s', $queueWithModel);
        return Cache::store(config('fair-queue.cache_store'))->increment($key);
    }

    /**
     * Decrement queue size
     *
     * @param string $queue
     * @param string $model
     * @return bool|int
     */
    private function decrementQueueSize(string $queue, string $model)
    {
        $queueWithModel = QueueNameBuilder::buildNameWithModelKey($model, $queue);
        $key = sprintf('fair-queue.queue_size.%1$s', $queueWithModel);
        return Cache::store(config('fair-queue.cache_store'))->decrement($key);
    }

    /**
     * Queue size
     *
     * @param string $queue
     * @param string $model
     * @return bool|int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function queueSize(string $queue, string $model)
    {
        $queueWithModel = QueueNameBuilder::buildNameWithModelKey($model, $queue);
        $key = sprintf('fair-queue.queue_size.%1$s', $queueWithModel);
        return Cache::store(config('fair-queue.cache_store'))->get($key, 0);
    }
}
