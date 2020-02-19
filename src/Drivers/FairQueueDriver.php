<?php

namespace NetLinker\FairQueue\Drivers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Queue\RedisQueue as BaseQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Events\JobDeleted;
use Laravel\Horizon\Events\JobPushed;
use Laravel\Horizon\Events\JobReleased;
use Laravel\Horizon\Events\JobReserved;
use Laravel\Horizon\Events\JobsMigrated;
use Laravel\Horizon\JobId;
use Laravel\Horizon\JobPayload;
use Laravel\Horizon\MasterSupervisor;
use Laravel\Horizon\Supervisor;
use NetLinker\FairQueue\Configuration\InstanceConfig;
use NetLinker\FairQueue\Events\QueueStarted;
use NetLinker\FairQueue\Events\QueueStarting;
use NetLinker\FairQueue\Events\WorkerStarted;
use NetLinker\FairQueue\Events\WorkerStarting;
use NetLinker\FairQueue\Facades\FairQueue;
use NetLinker\FairQueue\Models\FairIdentifier;
use NetLinker\FairQueue\Models\IdentifierModel;
use NetLinker\FairQueue\Models\ModelKey;
use NetLinker\FairQueue\Queues\QueueNameBuilder;

class FairQueueDriver extends BaseQueue
{
    /**
     * The job that last pushed to queue via the "push" method.
     *
     * @var object|string
     */
    protected $lastPushed;

    /**
     * Get the number of queue jobs that are ready to process.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function readyNow($queue = null)
    {
        return $this->getConnection()->llen($this->getQueue($queue));
    }

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
        $this->lastPushed = $job;

        $queue = $queue ?? 'default';


        $modelKey = ModelKey::get($queue);
        $modelId = $job->modelId ?? 0;
        $queue = QueueNameBuilder::build($queue, $modelKey, $modelId);
        dump(':::::: push queue modify:' . $queue);
        return parent::push($job, $data, $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $payload = (new JobPayload($payload))->prepare($this->lastPushed)->value;

        return tap(parent::pushRaw($payload, $queue, $options), function () use ($queue, $payload) {
            $this->event($this->getQueue($queue), new JobPushed($payload));
        });
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $job
     * @param  mixed  $data
     * @param  string  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $payload = (new JobPayload($this->createPayload($job, $queue, $data)))->prepare($job)->value;

        return tap(parent::laterRaw($delay, $payload, $queue), function () use ($payload, $queue) {
            $this->event($this->getQueue($queue), new JobPushed($payload));
        });
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function pop($queue = null)
    {
        $model = FairQueue::getModelFromQueue($queue);

        $job = $this->findFairPop($model, $queue);

        return tap($job, function ($result) use ($queue) {
            if ($result) {
                $this->event($this->getQueue($queue), new JobReserved($result->getReservedJob()));
            }
        });
    }


    /**
     * Migrate the delayed jobs that are ready to the regular queue.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function migrateExpiredJobs($from, $to)
    {
        return tap(parent::migrateExpiredJobs($from, $to), function ($jobs) use ($to) {
            $this->event($to, new JobsMigrated($jobs));
        });
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\RedisJob  $job
     * @return void
     */
    public function deleteReserved($queue, $job)
    {
        parent::deleteReserved($queue, $job);

        $this->event($this->getQueue($queue), new JobDeleted($job, $job->getReservedJob()));
    }

    /**
     * Delete a reserved job from the reserved queue and release it.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\RedisJob  $job
     * @param  int  $delay
     * @return void
     */
    public function deleteAndRelease($queue, $job, $delay)
    {
        parent::deleteAndRelease($queue, $job, $delay);

        $this->event($this->getQueue($queue), new JobReleased($job->getReservedJob()));
    }

    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     * @return int
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function size($queue = null)
    {
        $queue = $queue ?? 'default';

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
     * Fire the given event if a dispatcher is bound.
     *
     * @param string $queue
     * @param mixed $event
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function event($queue, $event)
    {
        if ($this->container && $this->container->bound(Dispatcher::class)) {
            $queue = Str::replaceFirst('queues:', '', $queue);

            $this->container->make(Dispatcher::class)->dispatch(
                $event->connection($this->getConnectionName())->queue($queue)
            );
        }
    }

    /**
     * Get a random ID string.
     *
     * @return string
     */
    protected function getRandomId()
    {
        return JobId::generate();
    }


    /**
     * Find fair pop
     *
     * @param $model
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function findFairPop($model, $queue = 'default')
    {
        $res = null;

        while(!$res){

            $fairId = FairIdentifier::get($model, $queue);

            if (!$this->isAllowPop($queue, $model, $fairId)){

                dump('fix');
                if ($fairId === IdentifierModel::maxId($model, $queue)){
                    break;
                }

                continue;
            }

            $queueName = QueueNameBuilder::build($queue, $model, $fairId);

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
     * @param $model
     * @param int $modelId
     * @return bool
     */
    private function isAllowPop(string $queue, $model, int $modelId):bool
    {
        dump(app()->make(SupervisorRepository::class)->names());
        return false;

        $instanceConfig = InstanceConfig::get();

        $active = Arr::get($instanceConfig, 'active');

        // not active queue in instance
        if (!$active){
            return false;
        }

        $modelConfig = Arr::get($instanceConfig, 'queues.' . QueueNameBuilder::buildOnlyName($model, $queue) . '.' . $modelKey);

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
