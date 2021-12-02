<?php


namespace NetLinker\FairQueue;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use NetLinker\FairQueue\Facades\FairQueue;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;

class HorizonManager
{

    /** @var Carbon $startedAt */
    public $startedAt;

    /** @var Horizon */
    public $horizon;

    /** @var $horizonResolver */
    public static $horizonResolver;

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
        $this->startedAt = now();
        $this->loadHorizon();
    }

    /**
     * Initialize configuration
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function init()
    {
        /** @var HorizonManager $config */
        app()->make(HorizonManager::class);
    }

    /**
     * Listen
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function listen()
    {
        /** @var HorizonManager $config */
        $manager = app()->make(HorizonManager::class);
        $manager->listenKill();
    }

    /**
     * Listen kill
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listenKill()
    {
        /** @var HorizonManager $config */
        $config = app()->make(HorizonManager::class);

        $key = sprintf('fair-queue.horizons.%1$s.kill', $config->horizon->uuid);

        /** @var Carbon $broadcastedAt */
        $broadcastedAt = Cache::store(config('fair-queue.cache_store'))->get($key);

        if ($broadcastedAt && $broadcastedAt->gt($config->startedAt)) {

            $this->kill();
        }
    }

    /**
     * @param $uuid
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function broadcastKill($uuid)
    {
        $key = sprintf('fair-queue.horizons.%1$s.kill', $uuid);
        Cache::store(config('fair-queue.cache_store'))->set($key, now());
    }

    /**
     * Load horizon
     */
    private function loadHorizon()
    {
        if (static::$horizonResolver) {
            $this->horizon = call_user_func(static::$horizonResolver);
        } else {
            $this->horizon = FairQueue::getHorizon();
        }

    }

    /**
     * Kill horizon
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function kill()
    {
        $masterSupervisor = $this->getMasterSupervisor();

        if (!posix_kill($masterSupervisor->pid, SIGTERM)) {
            $error = posix_strerror(posix_get_last_error());
            Log::error($error);
            dump($error);
        } else {
            $this->horizon->launched_at = null;
            $this->horizon->save();
            dump('horizon killed');
        }
    }

    /**
     * Get master supervisor
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getMasterSupervisor()
    {

        /** @var MasterSupervisorRepository $m */
        $masters = app()->make(MasterSupervisorRepository::class);

        $name = null;

        foreach ($masters->all() as $master) {

            if (Str::startsWith($master->name, $this->horizon->uuid)) {
                $name = $master->name;
                break;
            }
        }

        return $masters->find($name);
    }

}