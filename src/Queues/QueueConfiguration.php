<?php


namespace NetLinker\FairQueue\Queues;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use NetLinker\FairQueue\Facades\FairQueue;
use NetLinker\FairQueue\Sections\Accesses\Repositories\AccessRepository;
use NetLinker\FairQueue\Sections\Horizons\Repositories\HorizonRepository;
use NetLinker\FairQueue\Sections\Supervisors\Repositories\SupervisorRepository;

class QueueConfiguration
{

    /** @var array $queue */
    public $queues;

    /** @var $queuesResolver */
    public static $queuesResolver;

    /** @var array $allowAccesses */
    public $allowAccesses = [];

    /** @var array $excludeAccess */
    public $excludeAccess = [];

    /** @var Carbon $lastLoadedAt */
    public $lastLoadedAt;

    /** @var bool $horizon */
    public $horizon;

    /** @var bool $supervisor */
    public $supervisor;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * Load configuration
     */
    private function load()
    {
        $this->lastLoadedAt = now();
        $this->loadQueues();
        $this->loadAccesses();
        $this->loadHorizon();
        $this->loadSupervisor();
    }

    /**
     * Initialize configuration
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function init()
    {
        /** @var QueueConfiguration $config */
        app()->make(QueueConfiguration::class);
    }

    private function loadAccesses()
    {
        foreach ($this->queues as $queue){

            $model = FairQueue::getClassModelByQueue($queue->queue);

            $listObjectUuid = (new AccessRepository())->findWhere([
                'queue_uuid' => $queue->uuid,
                'type' => 'allow',
                'active' => true,
            ], ['object_uuid'])->pluck('object_uuid')->toArray();

            $this->allowAccesses[$queue->queue] = $model::whereIn('uuid', $listObjectUuid)->get(['id'])->pluck('id')->toArray();

            $listObjectUuid = (new AccessRepository())->findWhere([
                'queue_uuid' => $queue->uuid,
                'type' => 'exclude',
                'active' => true,
            ],['object_uuid'])->pluck('object_uuid')->toArray();

            $this->excludeAccess[$queue->queue] = $model::whereIn('uuid', $listObjectUuid)->get(['id'])->pluck('id')->toArray();

        }

    }


    /**
     * Load queue
     */
    public function loadQueues()
    {
        if (static::$queuesResolver) {
            $this->queues = call_user_func(static::$queuesResolver);
            return;
        }

        foreach (FairQueue::getQueues() as $queue){
            $this->queues[$queue->queue] = $queue;
        }
    }

    /**
     * Listen updated
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function listenUpdated()
    {
        /** @var QueueConfiguration $config */
        $config = app()->make(QueueConfiguration::class);

        $key = sprintf('fair-queue.queues.last_updated_at');

        /** @var Carbon $lastUpdatedAt */
        $lastUpdatedAt = Cache::store(config('fair-queue.cache_store'))->get($key);

        if ($lastUpdatedAt && $lastUpdatedAt->gt($config->lastLoadedAt)){
            $config->load();
        }
    }

    /**
     * Broadcast updated
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function broadcastUpdated(){
        $key = sprintf('fair-queue.queues.last_updated_at');
        Cache::store(config('fair-queue.cache_store'))->set($key, now());
    }

    /**
     * Load horizon
     */
    private function loadHorizon()
    {
        $queue = reset($this->queues);
        if ($queue){
            $this->horizon = (new HorizonRepository())->findWhere(['uuid' => $queue->horizon_uuid])->first();
        }

    }

    /**
     * Load supervisor
     */
    private function loadSupervisor()
    {
        $queue = reset($this->queues);
        if ($queue){
            $this->supervisor = (new SupervisorRepository())->findWhere(['uuid' => $queue->supervisor_uuid])->first();
        }
    }

    /**
     * Is queue active
     *
     * @param $queue
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function isQueueActive($queue){

        /** @var QueueConfiguration $config */
        $config = app()->make(QueueConfiguration::class);
        return $config->queues[$queue]->active && $config->horizon->active && $config->supervisor->active;
    }

    /**
     * Get refresh max model ID
     *
     * @param $queue
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function getRefreshMaxModelId($queue){

        /** @var QueueConfiguration $config */
        $config = app()->make(QueueConfiguration::class);
        return $config->queues[$queue]->refresh_max_model_id;
    }

    /**
     * Get allow accesses
     *
     * @param $queue
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function getAllowAccesses($queue){
        /** @var QueueConfiguration $config */
        $config = app()->make(QueueConfiguration::class);
        return $config->allowAccesses[$queue];
    }

    /**
     * Get exclude accesses
     *
     * @param $queue
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function getExcludeAccesses($queue){
        /** @var QueueConfiguration $config */
        $config = app()->make(QueueConfiguration::class);
        return $config->excludeAccess[$queue];
    }

}